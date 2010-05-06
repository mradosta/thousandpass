using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.IO;
using System.Runtime.InteropServices;
using System.Security.Permissions;
using System.Text;
using Microsoft.Win32;
using mshtml;
using SHDocVw;



namespace _1000Pass
{

    [
    ComVisible(true),
    Guid("8a194578-81ea-4850-9911-13ba2d71efbd"),
    ClassInterface(ClassInterfaceType.None)
    ]

    public class BHO:IObjectWithSite
    {

        WebBrowser webBrowser;
        HTMLDocument document;

        IEAccessible tabs = new IEAccessible();

        
        /*
        private void deleteFile(string file)
        {
            foreach (string FileName in System.IO.Directory.GetFiles(System.IO.Path.GetTempPath()))
            {
                try
                {
                    if (FileName == file)
                    {
                        System.IO.File.Delete(FileName);
                    }
                }
                catch { Exception ex; }//Certain files WILL give you access violations (index.dat)
            }
        }


        private void OnQuit()
        {
            deleteFile("1000pass_com.txt");
            deleteFile("1000pass_com_control.txt");
            //System.Windows.Forms.MessageBox.Show("quit");
        }
        */

        public void OnDocumentComplete(object pDisp, ref object URL)
        {
            //System.Windows.Forms.MessageBox.Show("quit");
            try
            {
                string line = "";
                TextReader tr = new StreamReader(System.IO.Path.GetTempPath() + "1000pass_com.txt");
                line = tr.ReadLine();
                tr.Close();

                /*
                TextWriter twDeleter = new StreamWriter(System.IO.Path.GetTempPath() + "1000pass_com.txt");
                twDeleter.WriteLine("");
                twDeleter.Close();
                */


                if (!string.IsNullOrEmpty(line))
                {
                    Data data = new Data(line);
                    IHTMLDocument3 htmlDoc = (IHTMLDocument3)webBrowser.Document;

                    try
                    {
                        // Scrap logout???
                        tr = new StreamReader(System.IO.Path.GetTempPath() + "1000pass_com_control.txt");
                        string control = tr.ReadLine();
                        tr.Close();

                        if (control == "scrap")
                        {

                            TextWriter tw = new StreamWriter(System.IO.Path.GetTempPath() + "1000pass_com_control.txt");
                            tw.WriteLine("");
                            tw.Close();

                            object Empty = "";
                            object oFlags;
                            object urlRedir = "";

                            string doc = htmlDoc.documentElement.outerHTML;
                            int ini_tag = doc.IndexOf(data.logout, 0);
                            if (ini_tag == -1)
                                return;
                            string tag = doc.Substring(ini_tag - 1, 1);
                            int fin_tag = doc.IndexOf(tag, ini_tag);
                            urlRedir = doc.Substring(ini_tag, fin_tag - ini_tag);

                            oFlags = 4; //navNoReadFromCache
                            webBrowser.Navigate2(ref urlRedir, ref oFlags, ref Empty, ref Empty, ref Empty);
                        }
                    }
                    catch (Exception ex) { }


                    if (htmlDoc != null)
                    {
                        // -------------Username ---------------
                        string[] tmpUsernameField = data.usernameField.Split('|');
                        IHTMLInputElement otxtUsername = (IHTMLInputElement)htmlDoc.getElementById(tmpUsernameField[1]);
                        if (otxtUsername != null)
                            otxtUsername.value = data.username;

                        // -------------Password ---------------
                        string[] tmpPasswordField = data.passwordField.Split('|');
                        IHTMLInputElement otxtPassword = (IHTMLInputElement)htmlDoc.getElementById(tmpPasswordField[1]);
                        if (otxtPassword != null)
                            otxtPassword.value = data.password;

                        // -------------Submit ---------------
                        string[] tmpForm = data.submit.Split('|');
                        IHTMLFormElement ofrm = (IHTMLFormElement)null;

                        switch (tmpForm[0])
                        {
                            case "id":
                            case "name":
                                ofrm = (IHTMLFormElement)htmlDoc.getElementById(tmpForm[1]);
                                break;
                            case "action":
                                foreach (IHTMLFormElement htmlFormElement in htmlDoc.getElementsByTagName("form"))
                                {
                                    if (htmlFormElement.action == tmpForm[1])
                                    {
                                        ofrm = htmlFormElement;
                                        break;
                                    }
                                }
                                break;
                            case "class":
                                foreach (IHTMLElement htmlElement in htmlDoc.getElementsByTagName("form"))
                                {
                                    if (htmlElement.className == tmpForm[1])
                                    {
                                        ofrm = (IHTMLFormElement)htmlElement;
                                        break;
                                    }
                                }
                                break;
                            default:
                                break;
                        }

                        if (ofrm != null)
                        {
                            ofrm.submit();
                        }
                    }
                }
            }
            catch (Exception ex) { }


            if (URL.ToString() == @"http://www.1000pass.com" || URL.ToString() == @"https://www.1000pass.com")
            {
                // Mark as add_on installed!
                IHTMLDocument3 htmlDoc1000Pass = (IHTMLDocument3)webBrowser.Document;
                IHTMLElement div1000pass_add_on = htmlDoc1000Pass.getElementById("1000pass_add_on");
                div1000pass_add_on.className = "installed";
                IHTMLElement div1000pass_add_on_version = htmlDoc1000Pass.getElementById("1000pass_add_on_version");
                div1000pass_add_on_version.className = "1.0";


                document = (HTMLDocument)webBrowser.Document;
                foreach (IHTMLElement htmlElement in document.getElementsByTagName("IMG"))
                {
                    
                    if (htmlElement.className == "remote_site_logo")
                    {
                        IHTMLStyle htmlStyle = (IHTMLStyle)htmlElement.style;
                        htmlStyle.cursor = "pointer";


                        DHTMLEventHandler Handler = new DHTMLEventHandler((IHTMLDocument2)webBrowser.Document);
                        Handler.Handler += new DHTMLEvent(logo_onclick);
                        htmlElement.onclick = Handler;

                        htmlElement.setAttribute("alreadyopened", "false", 0);
                    }
                }
            }


        }





        void logo_onclick(IHTMLEventObj e)
        {
            IHTMLElement plugin = e.srcElement.parentElement.parentElement.parentElement;

            System.Windows.Forms.WebBrowser webControl = new System.Windows.Forms.WebBrowser();
            webControl.Navigate("about:blank");
            webControl.Document.Write(plugin.innerHTML);


            string line = "";
            line += webControl.Document.GetElementById("plugin_identifier").InnerText + "#*#";
            line += webControl.Document.GetElementById("title").InnerText + "#*#";
            string url = webControl.Document.GetElementById("url").InnerText.Replace("&amp;", "&");
            line += url + "#*#";
            line += webControl.Document.GetElementById("username").InnerText + "#*#";
            line += webControl.Document.GetElementById("password").InnerText + "#*#";
            line += GetAttributeValue((IHTMLDOMNode)webControl.Document.GetElementById("username").DomElement, "class") + "#*#";
            line += GetAttributeValue((IHTMLDOMNode)webControl.Document.GetElementById("password").DomElement, "class") + "#*#";
            line += GetAttributeValue((IHTMLDOMNode)webControl.Document.GetElementById("submit").DomElement, "class") + "#*#";
            line += webControl.Document.GetElementById("logout_url").InnerText + "#*#";
            line += GetAttributeValue((IHTMLDOMNode)webControl.Document.GetElementById("logout_url").DomElement, "class");


            TextWriter tw = new StreamWriter(System.IO.Path.GetTempPath() + "1000pass_com.txt");
            tw.WriteLine(line);
            tw.Close();

            Data data = new Data(line);

            List<string> list = tabs.GetTabCaptions((IntPtr)webBrowser.HWND);
            String activeCaption = "";
            foreach (String caption in list) {
                if (caption.Replace(" ", "").ToLower().Contains(data.title.Replace(" ", "").ToLower()))
                {
                    activeCaption = caption;
                    break;
                }
            }



            object Empty = "";
            object oFlags;
            object urlRedir = "";

            if (!String.IsNullOrEmpty(activeCaption))
            {
                if (e.srcElement.getAttribute("alreadyopened", 0).ToString() == "true")
                {
                    tabs.ActivateTab((IntPtr)webBrowser.HWND, activeCaption);
                    return;
                }


                if (System.Windows.Forms.MessageBox.Show("Existe un sitio abierto para " + data.title + ". Desea continuar y cerrarlo?.", "", System.Windows.Forms.MessageBoxButtons.YesNo, System.Windows.Forms.MessageBoxIcon.Question) == System.Windows.Forms.DialogResult.Yes)
                //if (System.Windows.Forms.MessageBox.Show("There is an already opened " + data.title + ". Do you want to continue and close it?.", "", System.Windows.Forms.MessageBoxButtons.YesNo, System.Windows.Forms.MessageBoxIcon.Question) == System.Windows.Forms.DialogResult.Yes)
                {

                    // -------------Logout ---------------
                    oFlags = 4096; //navOpenInBackgroundTab
                    if (data.logoutMethod == "scrap")
                    {
                        tw = new StreamWriter(System.IO.Path.GetTempPath() + "1000pass_com_control.txt");
                        tw.WriteLine("scrap");
                        tw.Close();

                        tabs.ActivateTab((IntPtr)webBrowser.HWND, activeCaption);
                        tabs.CloseTab((IntPtr)webBrowser.HWND, activeCaption);

                        oFlags = 2048; //navOpenInNewTab
                        urlRedir = data.url;
                        webBrowser.Navigate2(ref urlRedir, ref oFlags, ref Empty, ref Empty, ref Empty);
                    }
                    else
                    {
                        urlRedir = data.logout;
                        webBrowser.Navigate2(ref urlRedir, ref oFlags, ref Empty, ref Empty, ref Empty);

                        tabs.ActivateTab((IntPtr)webBrowser.HWND, activeCaption);
                        tabs.CloseTab((IntPtr)webBrowser.HWND, activeCaption);
                    }

                }
                else {
                    return;
                }
            }
            else
            {
                oFlags = 2048; //navOpenInNewTab
                urlRedir = data.url;
                webBrowser.Navigate2(ref urlRedir, ref oFlags, ref Empty, ref Empty, ref Empty);
            }



            // Mark as already opened
            document = (HTMLDocument)webBrowser.Document;
            IHTMLElement tmpElement;
            foreach (IHTMLElement htmlElement in document.getElementsByTagName("IMG"))
            {

                if (htmlElement.className == "remote_site_logo")
                {
                    tmpElement = htmlElement.parentElement.parentElement.parentElement;

                    webControl = new System.Windows.Forms.WebBrowser();
                    webControl.Navigate("about:blank");
                    webControl.Document.Write(tmpElement.innerHTML);

                    if (webControl.Document.GetElementById("title").InnerText == data.title) {
                        htmlElement.setAttribute("alreadyopened", "false", 0);
                    }
                }
            }

            e.srcElement.setAttribute("alreadyopened", "true", 0);

        }


        private string GetAttributeValue(IHTMLDOMNode node, string attName)
        {
            IHTMLAttributeCollection2 atts = (IHTMLAttributeCollection2)node.attributes;
            if (atts != null)
            {
                IHTMLDOMAttribute attrib = atts.getNamedItem(attName);
                if (attrib != null && attrib.nodeValue != null)
                {
                    return attrib.nodeValue.ToString();
                }
            }
            return "";
        }


        /*
        public void OnBeforeNavigate2(object pDisp, ref object URL, ref object Flags, ref object TargetFrameName, ref object PostData, ref object Headers, ref bool Cancel)
        {
            document = (HTMLDocument)webBrowser.Document;

            foreach(IHTMLInputElement tempElement in document.getElementsByTagName("INPUT"))
            {
            if(tempElement.type.ToLower()=="password")
            {
            
                System.Windows.Forms.MessageBox.Show(tempElement.value);
            }
            
            }
        
        }*/



        #region BHO Internal Functions
        public static string BHOKEYNAME = "Software\\Microsoft\\Windows\\CurrentVersion\\Explorer\\Browser Helper Objects";

        [ComRegisterFunction]
        public static void RegisterBHO(Type type)
        {
            RegistryKey registryKey = Registry.LocalMachine.OpenSubKey(BHOKEYNAME, true);

            if (registryKey == null)
                registryKey = Registry.LocalMachine.CreateSubKey(BHOKEYNAME);

            string guid = type.GUID.ToString("B");
            RegistryKey ourKey = registryKey.OpenSubKey(guid);

            if (ourKey == null)
                ourKey = registryKey.CreateSubKey(guid);

            ourKey.SetValue("Alright", 1);
            registryKey.Close();
            ourKey.Close();
        }

        [ComUnregisterFunction]
        public static void UnregisterBHO(Type type)
        {
            RegistryKey registryKey = Registry.LocalMachine.OpenSubKey(BHOKEYNAME, true);
            string guid = type.GUID.ToString("B");

            if (registryKey != null)
                registryKey.DeleteSubKey(guid, false);
        }

        public int SetSite(object site)
        {

            if (site != null)
            {
                
                webBrowser = (WebBrowser)site;
                

                webBrowser.DocumentComplete += new DWebBrowserEvents2_DocumentCompleteEventHandler(this.OnDocumentComplete);
                //webBrowser.WindowClosing += new DWebBrowserEvents2_WindowClosingEventHandler(this.OnWindowClosing);
                //webBrowser.OnQuit += new DWebBrowserEvents2_OnQuitEventHandler(this.OnQuit);
                //webBrowser.BeforeNavigate2+=new DWebBrowserEvents2_BeforeNavigate2EventHandler(this.OnBeforeNavigate2);
            }
            else
            {
                webBrowser.DocumentComplete -= new DWebBrowserEvents2_DocumentCompleteEventHandler(this.OnDocumentComplete);
                //webBrowser.OnQuit -= new DWebBrowserEvents2_OnQuitEventHandler(this.OnQuit);
                //webBrowser.WindowClosing -= new DWebBrowserEvents2_WindowClosingEventHandler(this.OnWindowClosing);
                //webBrowser.BeforeNavigate2 -= new DWebBrowserEvents2_BeforeNavigate2EventHandler(this.OnBeforeNavigate2);
                webBrowser = null;
            }

            return 0;

        }

        public int GetSite(ref Guid guid, out IntPtr ppvSite)
        {
            IntPtr punk = Marshal.GetIUnknownForObject(webBrowser);
            int hr = Marshal.QueryInterface(punk, ref guid, out ppvSite);
            Marshal.Release(punk);

            return hr;
        }





        #endregion


    }
}
