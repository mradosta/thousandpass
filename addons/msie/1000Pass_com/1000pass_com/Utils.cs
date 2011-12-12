using System;
using System.Text;
using System.Net;
using System.IO;
using mshtml;
using System.Text.RegularExpressions;

namespace _1000Pass_com
{
    class Utils
    {
        static String BaseUrl = @"http://www.1000pass.com/sites_users/extension_add";
        //static String BaseUrl = @"http://10.0.0.53/thousandpass/sites_users/extension_add";
        //static String BaseUrl = @"http://192.168.0.8/thousandpass/sites_users/extension_add";
        public static String PluginFileName = @"1000pass_com_plugin.txt";
        public static String ControlFileName = @"1000pass_com_control.txt";
        public static String TokenFileName = @"1000pass_com_token.txt";
        public static String LogFileName = @"1000pass_com_log.txt";


        public static void l(Exception e)
        {
            string log = Environment.NewLine;
            log += @"=========================================================" + Environment.NewLine;
            log += DateTime.Now + Environment.NewLine;
            log += @"=========================================================" + Environment.NewLine;
            log += e.Message + Environment.NewLine;
            log += @"=========================================================" + Environment.NewLine;
            log += e.StackTrace + Environment.NewLine;
            //Utils.l(log);
            Utils.WriteToFile(Utils.LogFileName, log);
        }


        public static string decode64(string data) {
            try {
                System.Text.UTF8Encoding encoder = new System.Text.UTF8Encoding();
                System.Text.Decoder utf8Decode = encoder.GetDecoder();

                byte[] todecode_byte = Convert.FromBase64String(data);
                int charCount = utf8Decode.GetCharCount(todecode_byte, 0, todecode_byte.Length);
                char[] decoded_char = new char[charCount];
                utf8Decode.GetChars(todecode_byte, 0, todecode_byte.Length, decoded_char, 0);
                string result = new String(decoded_char);
                return result;

            } catch (Exception e) {
                throw new Exception("Error in base64Decode" + e.Message);
            }
        }


        public static void l(string msg) {
            System.Windows.Forms.MessageBox.Show(msg);
        }


        public static string GetDomain(String url)
        {
            Regex pattern = new Regex("(?<protocol>http(s)?|ftp)://(?<server>([A-Za-z0-9-]+\\.)*(?<basedomain>[A-Za-z0-9-]+\\.[A-Za-z0-9]+))+((:)?(?<port>[0-9]+)?(/?)(?<path>(?<dir>[A-Za-z0-9\\._\\-]+)(/){0,1}[A-Za-z0-9.-/]*)){0,1}");

            //string url = "http://my.domain.com:8000?arg1=this&arg2=that";
            System.Uri uri = new System.Uri(url);

            // get the port
            int port = uri.Port;

            // get the host name (my.domain.com)
            string host = uri.Host;

            // get the protocol
            //string protocol = uri.Scheme;

            // get everything before the query:
            //string cleanURL = uri.Scheme + "://" + uri.GetComponents(UriComponents.HostAndPort, UriFormat.UriEscaped);

            return host; //.ToLower().Replace(@"http://", "").Replace(@"https://", "");
        }

        public static string GetInnerText(IHTMLDOMNode node) 
        {
            IHTMLElement elem = (IHTMLElement)node;
            return elem.innerText;
        }

        public static string GetClassName(IHTMLDOMNode node)
        {
            IHTMLElement elem = (IHTMLElement)node;
            return elem.className;
        }


        public static void WriteToFile(string FileName, string line)
        {
            try
            {
                TextWriter tw = new StreamWriter(System.IO.Path.GetTempPath() + FileName);
                tw.WriteLine(line);
                tw.Close();
            }
            catch (Exception e)
            {
                Utils.l(e);
            }
        }


        public static string ReadFromFile(string FileName)
        {
            string line = "";
            try 
            {
                TextReader tr = new StreamReader(System.IO.Path.GetTempPath() + FileName);
                line = tr.ReadLine();
                tr.Close();
            }
            catch (Exception e)
            {
                Utils.l(e);
            }
            return line;
        }


        public static string FindXPath(IHTMLElement node)
        {
            if (node == null) {
                return "";
            }

            string xpath = @"##id=" + node.id;
            try
            {
                try
                {
                    xpath += @";name=" + node.getAttribute("name").ToString();
                }
                catch (Exception e)
                {
                    xpath += @";name=";
                }
                xpath += @";class=" + node.className;

                while (node.parentElement != null)
                {
                    IHTMLElementCollection elements = (IHTMLElementCollection)node.parentElement.children;
                    int c = 1;
                    foreach (IHTMLElement child in elements)
                    {
                        if (node.Equals(child))
                        {
                            break;
                        } 
                        else if (node.tagName == child.tagName) 
                        {
                            c++;
                        }
                    }

                    // ie bug??
                    if (node.tagName.ToUpper() == "BODY")
                    {
                        c--;
                    }

                    if (c > 1)
                    {
                        xpath = "/" + node.tagName + "[" + c + "]" + xpath;
                    }
                    else
                    {
                        xpath = "/" + node.tagName + xpath;
                    }

                    node = node.parentElement;
                }
            }
            catch (Exception e)
            {
                Utils.l(e);
            }

            return xpath;
        }


        public static string AddTo1000Pass(string token, string url, string title, string username, string usernameValue, string password, string passwordValue, string enter)
        {
            string responseFromServer = "";
            try
            {
                url = url.Replace(@"&", @"**||**");
                WebRequest request = WebRequest.Create(Utils.BaseUrl);
                request.Method = "POST";

                // Create POST data and convert it to a byte array.
                string postData = @"token=" + token + "&login_url=" + url + "&title=" + title + "&username_field=" + username + "|" + usernameValue + "&password_field=" + password + "|" + passwordValue + "&submit=" + enter;

                //Utils.WriteToFile("martin.txt", Utils.BaseUrl + @"?" + postData);
                //Utils.l(postData);

                byte[] byteArray = Encoding.UTF8.GetBytes(postData);
                request.ContentType = "application/x-www-form-urlencoded";
                request.ContentLength = byteArray.Length;
                Stream dataStream = request.GetRequestStream();
                dataStream.Write(byteArray, 0, byteArray.Length);
                dataStream.Close();
                WebResponse response = request.GetResponse();
                dataStream = response.GetResponseStream();
                StreamReader reader = new StreamReader(dataStream);

                // Read the content.
                responseFromServer = reader.ReadToEnd();

                reader.Close();
                dataStream.Close();
                response.Close();
            }
            catch (Exception e)
            {
                Utils.l(e);
            }

            return responseFromServer;
        }

    }
}
