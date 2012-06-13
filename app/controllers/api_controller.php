<?php

class ApiController extends AppController {

    var $name = 'Api'; //array('SitesUser', 'Api', 'SitesUsers');
    var $uses = array('Api', 'Site', 'User', 'Users', 'SitesUser', 'SitesUsers');
    var $components = array('Email', 'Captcha', 'Auth');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allowedActions = array('*');
    }

    function captcha() {
        $this->Captcha->show();
    }

    function change_password() {

        if (!empty($this->data)) {

            App::import('core', 'Sanitize');
            $user = $this->User->findById($this->Session->read('Auth.User.id'));
            $data = Sanitize::clean($this->data);

            if ($user['User']['password'] != Security::hash($data['User']['current_password'], null, true)) {
                $this->User->invalidate('current_password', __('Your current password do not match.', true));
            } elseif ($data['User']['password'] != $data['User']['repassword']) {
                $this->User->invalidate('repassword', __('Passwords do not match.', true));
            }


            if ($this->User->updateAll(array('User.password' => "'" . Security::hash($data['User']['password'], null, true) . "'"), array('User.id' => $user['User']['id']))) {

                $this->Session->setFlash(__('Your password has been changed.', true));
            } else {
                $this->Session->setFlash(__('Your password could not be changed.', true));
            }
        }
    }

    function addsite() {
        $uid = $_POST['uid'];

        $tmp = explode('|', $_POST['username_field']);
        $username_field = $tmp[0];
        $username = $tmp[1];

        if (!empty($_POST['extra_field'])) {
            $tmp = explode('|', $_POST['extra_field']);
            $extra_field = $tmp[0];
            $extra = $tmp[1];
        } else {
            $extra_field = '';
            $extra = '';
        }

        $tmp = explode('|', $_POST['password_field']);
        $password_field = $tmp[0];
        $password = $tmp[1];

        $this->Api->Site->recursive = -1;

        $exists = $this->Api->Site->findByLoginUrl($_POST['login_url']);

        if (empty($exists)) {

            if (strpos($_POST['login_url'], '/') !== false) {
                $logoName = array_pop(explode('//', $_POST['login_url']));
                if (strpos($logoName, '/') !== false) {
                    $logoName = array_shift(explode('/', $logoName));
                }
            } else {
                $logoName = $_POST['login_url'];
            }

            $logoName = str_replace('.', '_', $logoName) . '_' . uniqid() . '.jpg';
            $this->saveImage($_POST['login_url'], $logoName);
            $data['logo'] = $logoName;

            $data['login_url'] = $_POST['login_url'];
            $data['username_field'] = $username_field;
            $data['password_field'] = $password_field;
            $data['extra_field'] = $extra_field;
            $data['submit'] = $_POST['submit'];
            $data['title'] = $_POST['title'];
            $data['state'] = 'approved';

            if ($this->Api->Site->save(array('Site' => $data))) {
                $siteId = $this->Api->Site->id;
            } else {
                $res = __('No es posible guardar el sitio (B)', true);
            }
        } else {
            $siteId = $exists['Site']['id'];
        }


        $user = $this->Api->User->find('first', array(
            'conditions' => array('User.id' => $uid)
                )
        );

        /* echo '<pre>';
          print_r($siteId);
          echo '</pre>'; */

        if (!empty($user['User']['id'])) {
            if (!empty($siteId)) {
                $r = $this->Api->find('all', array(
                    'recursive' => -1,
                    'conditions' =>
                    array(
                        'SitesUser.site_id' => $siteId,
                        'SitesUser.user_id' => $user['User']['id'],
                        'SitesUser.username' => $username,
                        'SitesUser.password' => $password
                    )
                        )
                );

                if (!empty($r)) {

                    $res = __('El sitio ya esta agregado en su cuenta 1000Pass.com', true); //duplicated
                } else {

                    $data = array();
                    $data['user_id'] = $uid;
                    $data['order'] = 1000;
                    $data['site_id'] = $siteId;
                    $data['username'] = $username;
                    $data['password'] = $password;
                    $data['extra'] = $extra;

                    if ($this->SitesUser->save(array('SitesUser' => $data))) {
                        $res = __('El sitio se agrego correctamente a su cuenta de 1000Pass.com', true);
                    } else {
                        $res = __('No fue posible agregar el sitio a su cuenta. Contactese con admin@1000pass.com', true);
                    }
                }
            } else {
                $res = __('No fue posible agregar el sitio. Contactese con admin@1000pass.com', true);
            }
        } else {
            $res = __('No es posible identificar su usuario. Por favor, ingrese a 1000Pass.com y luego intente agregar el sitio nuevamente', true);
        }

        $this->output(array("status" => true, "message" => $res));
    }

    function getsites() {
        $uid = $_POST['uid'];

        $search = array();

        $mySites = $this->SitesUser->find('all', array(
            'order' => array('SitesUser.order' => 'asc'),
            'contain' => array('Site', 'ParentSitesUser'),
            'conditions' => array_merge(
                    $search, array('SitesUser.user_id' => $uid)
            )
                ));

        //if (count($mySites) == 0) {
        //  $this->output(array("status" => false, "message" => "No posee sitios"));
        //}
        //print_r( $mySites );

        $cond = array(
            array(
                'Site.id' => (array) Set::filter(Set::extract('/SitesUser/site_id', $mySites))
            )
        );

        //print_r( $cond );

        $sites = $this->Api->Site->find('all', array(
            'recursive' => -1,
            'conditions' => $cond
                )
        );

        $users = $this->Api->User->find('all', array(
            'recursive' => -1,
            'conditions' =>
            array(
                array(
                    'User.id' => Set::filter(Set::extract('/SitesUser/user_id', $mySites))
                )
            )
                )
        );

        $this->output(array("status" => true, "sitesUsers" => $mySites, "sites" => Set::combine($sites, '{n}.Site.id', '{n}.Site'), "users" => $users));

        /* $this->set('sitesUsers', $mySites);
          $this->set('sites', Set::combine($sites, '{n}.Site.id', '{n}.Site'));
          $this->set('users', Set::combine($users, '{n}.User.id', '{n}.User')); */
    }

    function delete_site() {
        $id = $_POST['sid'];

        if (!$id) {
            $this->output(array("status" => false, __('Invalid id for SitesUser', true) ) );
        }
        if ($this->SitesUser->del($id)) {
            $this->output(array("status" => true, __('SitesUser deleted', true) ) );
        }
    }

    /**
     *  The AuthComponent provides the needed functionality
     *  for login, so you can leave this function blank.
     */
    function login() {
        $this->data['User'] = array(
            "username" => $_POST['username'],
            "password" => $this->Auth->password($_POST['password'])
        );

        $this->Auth->hashPasswords($this->data);

        $login = $this->Auth->login($this->data);

        if (!(empty($this->data)) && $this->Auth->user()) {

            // delete prev in session
            $sql = 'DELETE FROM cake_sessions WHERE data LIKE \'%' . 's:8:"username";s:' . strlen($this->data['User']['username']) . ':"' . $this->data['User']['username'] . '"' . '%\'';
            $this->User->query($sql);

            $this->Session->write('add_on', array(
                'state' => $this->data['User']['1000pass_add_on'],
                'version' => $this->data['User']['1000pass_add_on_version']));


            // create token
            $token = uniqid();
            $this->User->id = $this->Auth->user('id');
            if ($this->User->saveField('token', $token)) {
                $this->Session->write('Auth.User.token', $token);
            }

            //$this->redirect($this->Auth->redirect());
            $this->output(array("status" => true, "token" => $token, "user" => $this->Session->read("Auth.User")));
            $this->logout();
        } else {
            $this->output(array("status" => false, "message" => __('Passwords do not match.', true)));
        }

        //$this->render('register');
    }

    function output($output) {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        echo json_encode($output);
        exit();
    }

    function display($what) {
        $this->$what();
    }

    function logout() {

        // delete token
        if ($this->Auth->isAuthorized()) {
            $this->User->id = $this->Auth->user('id');
            $this->User->saveField('token', '');
        }

        $this->Auth->logout();
    }

    function saveImage($url, $name) {

        // only for windows
        if (PHP_OS == 'Linux') {
            return;
        }


        $saveFileAs = IMAGES . 'logos' . DS . $name;

        // run in the bg
        $WshShell = new COM('WScript.Shell');
        $r = $WshShell->Run('e:\wamp\bin\php\php5.3.0\php.exe e:\wamp\www\app\webroot\files\helpers\capture.php ' . $url . ' ' . $saveFileAs, 0, false);
    }

    /* function beforeFilter() {

      $this->Auth->autoRedirect = false;

      //d($this->User);
      //$this->User->birthdate = date("Y-m-d");
      //$this->User->saveField('last_login', date('Y-m-d H:i:s'));

      $this->Auth->allow(array('get_contacts', 'captcha', 'register', 'recover_password', 'terms_of_service', 'help'));



      return parent::beforeFilter();
      } */

    function get_contacts($id) {

        $this->User->SitesUser->recursive = -1;
        $sitesUser = $this->User->SitesUser->findById($id);

        if (!empty($sitesUser)) {


            $domain = array_pop(explode('@', $sitesUser['SitesUser']['username']));

            if ($domain == 'gmail.com') {
                $plugin = 'gmail';
            } elseif ($domain == 'hotmail.com') {
                $plugin = 'hotmail';
            } elseif (strpos($domain, 'yahoo') !== false) {
                $plugin = 'yahoo';
            }

            App::import('Vendor', 'OpenInviter', array('file' => 'openinviter.php'));
            $inviter = new OpenInviter();
            $inviter->getPlugins();
            $inviter->startPlugin($plugin);
            $inviter->login($sitesUser['SitesUser']['username'], $sitesUser['SitesUser']['password']);
            $errs = $inviter->getInternalError();

            if (!empty($errs)) {
                $this->Session->setFlash($errs, true);
                $this->redirect(array('controller' => 'sites_users', 'action' => 'index', 'true'));
            } else {
                $contacts = $inviter->getMyContacts();
            }

            $this->__sendEmail(
                    array('template' => 'invite', 'subject' => $this->Session->read('Auth.User.name') . ' ' . $this->Session->read('Auth.User.lastname') . ' ' . __('Invites you to 1000Pass.com', true)), $contacts);

            $this->Session->setFlash(__('Thanks for inviting your friends to 1000pass.com!', true), true);
            $this->redirect(array('controller' => 'sites_users', 'action' => 'index', 'true'));
        }
    }

    function check_password($password) {

        Configure::write('debug', 0);
        $this->layout = 'ajax';

        App::import('Core', 'Security');
        $user = $this->User->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'User.username' => $this->Session->read('Auth.User.username'),
                'User.password' => Security::hash($password, null, true))));

        if (empty($user)) {
            $this->set('data', 'err');
        } else {
            $this->set('data', 'ok');
        }
    }

    /**
     * Reset users password and send it by email.
     */
    function recover_password() {

        if (!empty($this->data)) {
            if (!$this->Captcha->protect()) {
                $this->User->invalidate('captcha', __('Validation text error. Try again', true));
                //} elseif (empty($this->data['User']['username'])) {
                //	$this->User->invalidate('username', __('Must enter the username.', true));
            } elseif (empty($this->data['User']['email'])) {
                $this->User->invalidate('email', __('Must enter the email.', true));
            }

            $user = $this->User->find('first', array('conditions' => array(
                    //'User.username' => $this->data['User']['username'],
                    'User.email' => $this->data['User']['email'])));
            if (!empty($user)) {
                $uppercase = range('A', 'Z');
                $numeric = range(0, 9);
                $charPool = array_merge($uppercase, $numeric);

                $poolLength = count($charPool) - 1;
                $newPassword = '';
                for ($i = 0; $i < 8; $i++) {
                    $newPassword .= $charPool[mt_rand(0, $poolLength)];
                }

                App::import('Core', 'Security');
                $this->User->save(array('User' => array(
                        'id' => $user['User']['id'],
                        'password' => Security::hash($newPassword, null, true))));

                $this->__sendEmail(
                        array('template' => 'recover_password', 'subject' => __('1000Pass.com - Password Recovery Service', true)), array($user['User']['username'] => $user['User']['username']), array('username' => $user['User']['username'], 'newpassword' => $newPassword));
                $this->Session->setFlash(__('Your password has been send to your email.', true));
            } else {
                $this->Session->setFlash(__('Username and/or email does not exists.', true));
            }
        }
    }

    private function __sendEmail($mailInfo, $destinations, $data = null) {

        /* SMTP Options
          $this->Email->smtpOptions = array(
          'port'		=> '25',
          'timeout'	=> '300',
          'host' 		=> 'mail.1000pass.com',
          'username'	=> 'info@1000pass.com',
          'password'	=> 'info2010',
          'client' 	=> 'smtp_helo_hostname'
          );
         */
        /*
          $this->Email->smtpOptions = array(
          'port'		=> '25',
          'timeout'	=> '300',
          'host' 		=> 'mail.riesgoonline.com',
          'username'	=> 'info@1000pass.com',
          'password'	=> 'info2010',
          'client' 	=> 'smtp_helo_hostname'
          );
         */
        $this->Email->delivery = 'smtp';

        foreach ($destinations as $name => $email) {
            $this->Email->reset();
            $this->Email->to = $name . ' <' . $email . '>';
            $this->Email->subject = $mailInfo['subject'];
            $this->Email->replyTo = 'info@1000pass.com';
            $this->Email->from = '1000Pass.com <info@1000pass.com>';
            $this->Email->template = $mailInfo['template'];
            $this->Email->sendAs = 'html';
            $this->set('data', $data);
            $this->Email->send();
        }
    }

    function terms_of_service() {
        $this->view = 'Media';
        $params = array(
            'id' => Configure::read('Config.language') . '1000pass.pdf',
            'name' => __('Terms_Of_Service', true),
            'download' => true,
            'extension' => 'pdf',
            'path' => APP . 'files' . DS
        );
        $this->set($params);
    }

                /**
             * <select name="data[User][country]" id="UserCountry">
              <option value=""></option>
              <optgroup label="North America">
              <option value="Canada">Canada</option>
              <option value="Greenland">Greenland</option>
              <option value="Mexico">Mexico</option>
              <option value="United States">United States</option>
              <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
              <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
              </optgroup>
              <optgroup label="Central America">
              <option value="Anguilla">Anguilla</option>
              <option value="Antigua and Barbuda">Antigua and Barbuda</option>
              <option value="Aruba">Aruba</option>
              <option value="Bahamas">Bahamas</option>
              <option value="Barbados">Barbados</option>
              <option value="Belize">Belize</option>
              <option value="Bermuda">Bermuda</option>
              <option value="Virgin Islands, British">Virgin Islands, British</option>
              <option value="Cayman Islands">Cayman Islands</option>
              <option value="Costa Rica">Costa Rica</option>
              <option value="Cuba">Cuba</option>
              <option value="Dominica">Dominica</option>
              <option value="Dominican Republic">Dominican Republic</option>
              <option value="El Salvador">El Salvador</option>
              <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
              <option value="Grenada">Grenada</option>
              <option value="Guadeloupe">Guadeloupe</option>
              <option value="Guatemala">Guatemala</option>
              <option value="Haiti">Haiti</option>
              <option value="Honduras">Honduras</option>
              <option value="Jamaica">Jamaica</option>
              <option value="Martinique">Martinique</option>
              <option value="Montserrat">Montserrat</option>
              <option value="Netherlands Antilles">Netherlands Antilles</option>
              <option value="Nicaragua">Nicaragua</option>
              <option value="Panama">Panama</option>
              <option value="Puerto Rico">Puerto Rico</option>
              <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
              <option value="Saint Lucia">Saint Lucia</option>
              <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
              <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option>
              <option value="Trinidad and Tobago">Trinidad and Tobago</option>
              <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
              </optgroup>
              <optgroup label="South America">
              <option value="Argentina">Argentina</option>
              <option value="Bolivia">Bolivia</option>
              <option value="Brazil">Brazil</option>
              <option value="Chile">Chile</option>
              <option value="Colombia">Colombia</option>
              <option value="Ecuador">Ecuador</option>
              <option value="French Guiana">French Guiana</option>
              <option value="Guyana">Guyana</option>
              <option value="Paraguay">Paraguay</option>
              <option value="Peru">Peru</option>
              <option value="Suriname">Suriname</option>
              <option value="Uruguay">Uruguay</option>
              <option value="Venezuela">Venezuela</option>
              </optgroup>
              <optgroup label="Africa">
              <option value="Algeria">Algeria</option>
              <option value="Angola">Angola</option>
              <option value="Benin">Benin</option>
              <option value="Botswana">Botswana</option>
              <option value="Burkina">Burkina</option>
              <option value="Burundi">Burundi</option>
              <option value="Cameroon">Cameroon</option>
              <option value="Cape Verde">Cape Verde</option>
              <option value="Central African">Central African</option>
              <option value="dot Republic">dot Republic</option>
              <option value="Chad">Chad</option>
              <option value="Comoros">Comoros</option>
              <option value="Congo">Congo</option>
              <option value="dot (Dem. Rep.)">dot (Dem. Rep.)</option>
              <option value="Djibouti">Djibouti</option>
              <option value="Egypt">Egypt</option>
              <option value="Equatorial Guinea">Equatorial Guinea</option>
              <option value="Eritrea">Eritrea</option>
              <option value="Ethiopia">Ethiopia</option>
              <option value="Gabon">Gabon</option>
              <option value="Gambia">Gambia</option>
              <option value="Ghana">Ghana</option>
              <option value="Guinea">Guinea</option>
              <option value="Guinea-Bissau">Guinea-Bissau</option>
              <option value="Ivory Coast">Ivory Coast</option>
              <option value="Kenya">Kenya</option>
              <option value="Lesotho">Lesotho</option>
              <option value="Liberia">Liberia</option>
              <option value="Libya">Libya</option>
              <option value="Madagascar">Madagascar</option>
              <option value="Malawi">Malawi</option>
              <option value="Mali">Mali</option>
              <option value="Mauritania">Mauritania</option>
              <option value="Mauritius">Mauritius</option>
              <option value="Morocco">Morocco</option>
              <option value="Mozambique">Mozambique</option>
              <option value="Namibia">Namibia</option>
              <option value="Niger">Niger</option>
              <option value="Nigeria">Nigeria</option>
              <option value="Rwanda">Rwanda</option>
              <option value="Sao Tome">Sao Tome</option>
              <option value="dot and Principe">dot and Principe</option>
              <option value="Senegal">Senegal</option>
              <option value="Seychelles">Seychelles</option>
              <option value="Sierra Leone">Sierra Leone</option>
              <option value="Somalia">Somalia</option>
              <option value="South Africa">South Africa</option>
              <option value="Sudan">Sudan</option>
              <option value="Swaziland">Swaziland</option>
              <option value="Tanzania">Tanzania</option>
              <option value="Togo">Togo</option>
              <option value="Tunisia">Tunisia</option>
              <option value="Uganda">Uganda</option>
              <option value="Zambia">Zambia</option>
              <option value="Zimbabwe">Zimbabwe</option>
              </optgroup>
              <optgroup label="Asia">
              <option value="Afghanistan">Afghanistan</option>
              <option value="Bahrain">Bahrain</option>
              <option value="Bangladesh">Bangladesh</option>
              <option value="Bhutan">Bhutan</option>
              <option value="Brunei">Brunei</option>
              <option value="Burma (Myanmar)">Burma (Myanmar)</option>
              <option value="Cambodia">Cambodia</option>
              <option value="China">China</option>
              <option value="East Timor">East Timor</option>
              <option value="India">India</option>
              <option value="Indonesia">Indonesia</option>
              <option value="Iran">Iran</option>
              <option value="Iraq">Iraq</option>
              <option value="Israel">Israel</option>
              <option value="Japan">Japan</option>
              <option value="Jordan">Jordan</option>
              <option value="Kazakhstan">Kazakhstan</option>
              <option value="Korea (north)">Korea (north)</option>
              <option value="Korea (south)">Korea (south)</option>
              <option value="Kuwait">Kuwait</option>
              <option value="Kyrgyzstan">Kyrgyzstan</option>
              <option value="Laos">Laos</option>
              <option value="Lebanon">Lebanon</option>
              <option value="Malaysia">Malaysia</option>
              <option value="Maldives">Maldives</option>
              <option value="Mongolia">Mongolia</option>
              <option value="Nepal">Nepal</option>
              <option value="Oman">Oman</option>
              <option value="Pakistan">Pakistan</option>
              <option value="Philippines">Philippines</option>
              <option value="Qatar">Qatar</option>
              <option value="Russian">Russian</option>
              <option value="dotFederation">dotFederation</option>
              <option value="Saudi Arabia">Saudi Arabia</option>
              <option value="Singapore">Singapore</option>
              <option value="Sri Lanka">Sri Lanka</option>
              <option value="Syria">Syria</option>
              <option value="Tajikistan">Tajikistan</option>
              <option value="Thailand">Thailand</option>
              <option value="Turkey">Turkey</option>
              <option value="Turkmenistan">Turkmenistan</option>
              <option value="United Arab">United Arab</option>
              <option value="dot Emirates">dot Emirates</option>
              <option value="Uzbekistan">Uzbekistan</option>
              <option value="Vietnam">Vietnam</option>
              <option value="Yemen">Yemen</option>
              </optgroup>
              <optgroup label="Europe">
              <option value="Albania">Albania</option>
              <option value="Andorra">Andorra</option>
              <option value="Armenia">Armenia</option>
              <option value="Austria">Austria</option>
              <option value="Azerbaijan">Azerbaijan</option>
              <option value="Belarus">Belarus</option>
              <option value="Belgium">Belgium</option>
              <option value="Bosnia">Bosnia</option>
              <option value="dotand Herzegovina">dotand Herzegovina</option>
              <option value="Bulgaria">Bulgaria</option>
              <option value="Croatia">Croatia</option>
              <option value="Cyprus">Cyprus</option>
              <option value="Czech Republic">Czech Republic</option>
              <option value="Denmark">Denmark</option>
              <option value="Estonia">Estonia</option>
              <option value="Finland">Finland</option>
              <option value="France">France</option>
              <option value="Georgia">Georgia</option>
              <option value="Germany">Germany</option>
              <option value="Greece">Greece</option>
              <option value="Hungary">Hungary</option>
              <option value="Iceland">Iceland</option>
              <option value="Ireland">Ireland</option>
              <option value="Italy">Italy</option>
              <option value="Latvia">Latvia</option>
              <option value="Liechtenstein">Liechtenstein</option>
              <option value="Lithuania">Lithuania</option>
              <option value="Luxembourg">Luxembourg</option>
              <option value="Macedonia">Macedonia</option>
              <option value="Malta">Malta</option>
              <option value="Moldova">Moldova</option>
              <option value="Monaco">Monaco</option>
              <option value="Montenegro">Montenegro</option>
              <option value="Netherlands">Netherlands</option>
              <option value="Norway">Norway</option>
              <option value="Poland">Poland</option>
              <option value="Portugal">Portugal</option>
              <option value="Romania">Romania</option>
              <option value="San Marino">San Marino</option>
              <option value="Serbia">Serbia</option>
              <option value="Slovakia">Slovakia</option>
              <option value="Slovenia">Slovenia</option>
              <option value="Spain">Spain</option>
              <option value="Sweden">Sweden</option>
              <option value="Switzerland">Switzerland</option>
              <option value="Ukraine">Ukraine</option>
              <option value="United Kingdom">United Kingdom</option>
              <option value="Vatican City">Vatican City</option>
              </optgroup>
              <optgroup label="Oceania">
              <option value="American Samoa">American Samoa</option>
              <option value="Australia">Australia</option>
              <option value="Christmas Island">Christmas Island</option>
              <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
              <option value="Cook Islands">Cook Islands</option>
              <option value="Easter Island">Easter Island</option>
              <option value="Fiji">Fiji</option>
              <option value="Guam">Guam</option>
              <option value="Indonesia">Indonesia</option>
              <option value="Kiribati">Kiribati</option>
              <option value="Marshall Islands">Marshall Islands</option>
              <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
              <option value="Nauru">Nauru</option>
              <option value="New Caledonia">New Caledonia</option>
              <option value="New Zealand">New Zealand</option>
              <option value="Niue">Niue</option>
              <option value="Norfolk Island">Norfolk Island</option>
              <option value="Northern Mariana Islands">Northern Mariana Islands</option>
              <option value="Palau">Palau</option>
              <option value="Papua New Guinea">Papua New Guinea</option>
              <option value="Pitcairn">Pitcairn</option>
              <option value="French Polynesia">French Polynesia</option>
              <option value="Samoa">Samoa</option>
              <option value="Solomon Islands">Solomon Islands</option>
              <option value="Tokelau">Tokelau</option>
              <option value="Tonga">Tonga</option>
              <option value="Tuvalu">Tuvalu</option>
              <option value="Vanuatu">Vanuatu</option>
              </optgroup>
              <optgroup label="Antartica">
              <option value="Antarctica">Antarctica</option>
              <option value="Bouvet Island">Bouvet Island</option>
              <option value="French Southern Territories">French Southern Territories</option>
              <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option>
              <option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option>
              </optgroup>
              </select>
             */
    function register() {
        $this->Session->destroy();
        
        $this->data['User'] = array(
            "name" => $_POST['name'],
            "lastname" => $_POST['lastname'],
            "birthdate" => array("day" => $_POST['birthday'], "month" => $_POST['birthmonth'], "year" => $_POST['birthyear']),
            "sex" => $_POST['sex'], // f or m
            "country" => $_POST['country'],
            "username" => $_POST['username'],
            "password" => $this->Auth->password($_POST['password']),
            "repassword" => $_POST['password'],
            "captcha"   => 000000,
            "terms_of_service" => $_POST['terms_of_service'], // 0 or 1
        );

        $this->Auth->hashPasswords($this->data);
        
        //App::import('Model', 'User');
        //$this->User = new User();

        if (!empty($this->data)) { 
            // relax validation
            if (empty($this->data['User']['terms_of_service'])) {
                $this->output(array("status" => false, "message" => __('Must accept the Terms of Service', true)));
            } else {
                App::import('core', 'Sanitize');
                $this->User->data = Sanitize::clean($this->data);
                
                if ( $this->User->save( ) ) {
                    $this->__sendEmail(
                            array('template' => 'sign_up',
                                'subject' => __('Welcome to 1000Pass.com', true)), 
                            array($this->data['User']['name'] . ' ' . $this->data['User']['lastname'] => $this->data['User']['username']), 
                            $this->data);
                    //$this->Session->setFlash();
                    $this->data['User']['id'] = $this->User->id;
                    unset( $this->data['User']['birthdate'] );
                    unset( $this->data['User']['password'] );
                    unset( $this->data['User']['repassword'] );
                    unset( $this->data['User']['captcha'] );
                    unset( $this->data['User']['terms_of_service'] );
                    //$this->redirect(array('controller' => 'sites_users', 'action' => 'index'));
                    //$this->redirect($this->Auth->redirect());
                    $this->output(array("status" => true, "message" => __('Thanks for signing up at 1000Pass.com.', true), "user" => $this->data['User']));

                    //$this->logout();
                } else {
                    $this->output(array("status" => false, "message" => __('The User could not be saved. Please, try again.', true)));
                }
            }
        }
    }

    function index() {
        /* $this->layout = 'admin';
          $this->User->recursive = 0;
          $this->set('users', $this->paginate()); */
    }

    function view($id = null) {
        $this->layout = 'admin';
        if (!$id) {
            $this->Session->setFlash(__('Invalid User.', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    function add() {
        $this->layout = 'admin';
        if (!empty($this->data)) {
            $this->User->create();
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('The User has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
            }
        }
        $sites = $this->User->Site->find('list');
        $this->set(compact('sites'));
    }

    function edit($id = null) {
        $this->layout = 'admin';
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid User', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('The User has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->User->read(null, $id);
        }
        $sites = $this->User->Site->find('list');
        $this->set(compact('sites'));
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for User', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->User->del($id)) {
            $this->Session->setFlash(__('User deleted', true));
            $this->redirect(array('action' => 'index'));
        }
    }

}

?>