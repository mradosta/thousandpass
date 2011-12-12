/****************************** Module Header ******************************\
* Module Name:  OpenImageBHO.cs
* Project:      CSCustomIEContextMenu
* Copyright (c) Microsoft Corporation.
* 
* The class OpenImageBHO is a Browser Helper Object which runs within Internet
* Explorer and offers additional services.
* 
* A BHO is a dynamic-link library (DLL) capable of attaching itself to any new 
* instance of Internet Explorer or Windows Explorer. Such a module can get in touch 
* with the browser through the container's site. In general, a site is an intermediate
* object placed in the middle of the container and each contained object. When the
* container is Internet Explorer (or Windows Explorer), the object is now required 
* to implement a simpler and lighter interface called IObjectWithSite. 
* It provides just two methods SetSite and GetSite. 
* 
* This class is used to set the IDocHostUIHandler of the HtmlDocument.
* 
* This source is subject to the Microsoft Public License.
* See http://www.microsoft.com/opensource/licenses.mspx#Ms-PL.
* All other rights reserved.
* 
* THIS CODE AND INFORMATION IS PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND, 
* EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED 
* WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
\***************************************************************************/

using System;
using System.Runtime.InteropServices;
using _1000Pass_com.NativeMethods;
using Microsoft.Win32;
using SHDocVw;
using mshtml;
using System.IO;
using System.Text.RegularExpressions;
using System.Threading;
using System.Windows.Forms;
using System.Collections.Generic;

namespace _1000Pass_com
{
    /// <summary>
    /// Set the GUID of this class and specify that this class is ComVisible.
    /// A BHO must implement the interface IObjectWithSite. 
    /// </summary>
    [ComVisible(true)]
    [ClassInterface(ClassInterfaceType.None)]
    [Guid("AA0B1334-E7F5-4F75-A1DE-0993098AAF01")]
    public class _1000Pass_BHO : IObjectWithSite, IDisposable
    {

        const string VERSION = @"1.0";

        private bool disposed = false;

        // To register a BHO, a new key should be created under this key.
        private const string BHORegistryKey =
            "Software\\Microsoft\\Windows\\CurrentVersion\\Explorer\\Browser Helper Objects";

        // Current IE instance. For IE7 or later version, an IE Tab is just an IE instance.
        private InternetExplorer ieInstance;

        // Current document
        private HTMLDocument document;

        // tabs
        private IEAccessible tabs = new IEAccessible();

        private OpenMenuHandler openImageDocHostUIHandler;


        #region Com Register/UnRegister Methods
        /// <summary>
        /// When this class is registered to COM, add a new key to the BHORegistryKey 
        /// to make IE use this BHO.
        /// On 64bit machine, if the platform of this assembly and the installer is x86,
        /// 32 bit IE can use this BHO. If the platform of this assembly and the installer
        /// is x64, 64 bit IE can use this BHO.
        /// </summary>
        [ComRegisterFunction]
        public static void RegisterBHO(Type t)
        {

            // If the key exists, CreateSubKey will open it.
            RegistryKey bhosKey = Registry.LocalMachine.CreateSubKey(
                BHORegistryKey,
                RegistryKeyPermissionCheck.ReadWriteSubTree);

            // 32 digits separated by hyphens, enclosed in braces: 
            // {00000000-0000-0000-0000-000000000000}
            string bhoKeyStr = t.GUID.ToString("B");

            RegistryKey bhoKey = bhosKey.CreateSubKey(bhoKeyStr);

            // NoExplorer:dword = 1 prevents the BHO to be loaded by Explorer
            bhoKey.SetValue("NoExplorer", 1);
            bhosKey.Close();
            bhoKey.Close();
        }

        /// <summary>
        /// When this class is unregistered from COM, delete the key.
        /// </summary>
        [ComUnregisterFunction]
        public static void UnregisterBHO(Type t)
        {
            RegistryKey bhosKey = Registry.LocalMachine.OpenSubKey(BHORegistryKey, true);
            string guidString = t.GUID.ToString("B");
            if (bhosKey != null)
            {
                bhosKey.DeleteSubKey(guidString, false);
            }

            bhosKey.Close();
        }


        #endregion

        #region IObjectWithSite Members
        /// <summary>
        /// This method is called when the BHO is instantiated and when
        /// it is destroyed. The site is an object implemented the 
        /// interface InternetExplorer.
        /// </summary>
        /// <param name="site"></param>
        public void SetSite(Object site)
        {
            if (site != null)
            {
                this.ieInstance = (InternetExplorer) site;

                // Register the context menu handler.
                openImageDocHostUIHandler = new OpenMenuHandler(this.ieInstance);

                // Register the DocumentComplete event.
                ieInstance.DocumentComplete +=
                    new DWebBrowserEvents2_DocumentCompleteEventHandler(IeInstance_DocumentComplete);

            }
        }

        /// <summary>
        /// Retrieves and returns the specified interface from the last site
        /// set through SetSite(). The typical implementation will query the
        /// previously stored pUnkSite pointer for the specified interface.
        /// </summary>
        public void GetSite(ref Guid guid, out Object ppvSite)
        {
            IntPtr punk = Marshal.GetIUnknownForObject(ieInstance);
            ppvSite = new object();
            IntPtr ppvSiteIntPtr = Marshal.GetIUnknownForObject(ppvSite);
            int hr = Marshal.QueryInterface(punk, ref guid, out ppvSiteIntPtr);
            Marshal.ThrowExceptionForHR(hr);
            Marshal.Release(punk);
        }
        #endregion

        #region event handler


        /// <summary>
        /// Handle the DocumentComplete event.
        /// </summary>
        /// <param name="pDisp">
        /// The pDisp is an an object implemented the interface InternetExplorer.
        /// By default, this object is the same as the ieInstance, but if the page 
        /// contains many frames, each frame has its own document.
        /// </param>
        void IeInstance_DocumentComplete(object pDisp, ref object URL)
        {
            if (ieInstance == null) 
            {
                return;
            }


            // get the url
            string url = URL as string;
            if (string.IsNullOrEmpty(url) || url.Equals(@"about:Tabs", StringComparison.OrdinalIgnoreCase) || url.Equals("about:blank", StringComparison.OrdinalIgnoreCase))
            {
                return;
            }


            // http://borderstylo.com/posts/115-browser-wars-2-dot-0-the-plug-in
            SHDocVw.WebBrowser browser = (SHDocVw.WebBrowser)ieInstance;
            if (browser.ReadyState != SHDocVw.tagREADYSTATE.READYSTATE_COMPLETE)
            {
                return;
            }


            // Set the handler of the document in InternetExplorer.
            NativeMethods.ICustomDoc customDoc = (NativeMethods.ICustomDoc)ieInstance.Document;
            customDoc.SetUIHandler(openImageDocHostUIHandler);


            // sets the document
            this.document = (HTMLDocument) ieInstance.Document;


            try
            {
                if (this.document.url.Contains(@"thousandpass") || this.document.url.Contains(@"1000pass.com"))
                {
                    // Mark the add_on as installed!
                    IHTMLElement div1000pass_add_on = this.document.getElementById("1000pass_add_on");
                    div1000pass_add_on.className = @"installed";
                    IHTMLElement div1000pass_add_on_version = this.document.getElementById("1000pass_add_on_version");
                    div1000pass_add_on_version.innerText = VERSION;


                    // Try to save the token
                    string token = div1000pass_add_on.getAttribute("token").ToString();
                    if (!String.IsNullOrEmpty(token))
                    {
                        Utils.WriteToFile(Utils.TokenFileName, token);
                    }


                    foreach (IHTMLElement htmlElement in document.getElementsByTagName("IMG"))
                    {
                        if (htmlElement.className == "remote_site_logo")
                        {
                            IHTMLStyle htmlStyle = (IHTMLStyle)htmlElement.style;
                            htmlStyle.cursor = "pointer";


                            DHTMLEventHandler Handler = new DHTMLEventHandler((IHTMLDocument2)ieInstance.Document);
                            Handler.Handler += new DHTMLEvent(Logo_OnClick);
                            htmlElement.onclick = Handler;

                            htmlElement.setAttribute("alreadyopened", "false", 0);
                        }
                    }
                }
                else
                {
                    // Must run on a thread to guaranty the page has finished loading (js loading)
                    // http://stackoverflow.com/questions/3514945/running-a-javascript-function-in-an-instance-of-internetexplorer
                    System.Threading.ThreadPool.QueueUserWorkItem((o) =>
                    {
                        System.Threading.Thread.Sleep(500);
                        try
                        {
                            Thread aThread = new Thread(bind);
                            aThread.SetApartmentState(ApartmentState.STA);
                            aThread.Start();
                        }
                        catch (Exception ee)
                        {
                            Utils.l(ee);
                        }
                    }, browser);
                }
            }
            catch (Exception e)
            {
                Utils.l(e);
            }
        }



        private void bind()
        {
            try
            {
                string line = Utils.ReadFromFile(Utils.PluginFileName);
                if (!string.IsNullOrEmpty(line))
                {

                    Data data = new Data(line);

                    HTMLInputElement usernameElement = (HTMLInputElement)FindElement(data.usernameField);
                    if (usernameElement == null)
                    {
                        // Utils.l("No es posible encontrar el campo Usuario.");
                        return;
                    }
                    else 
                    {
                        usernameElement.value = data.username;
                    }
                    
                    HTMLInputElement passwordElement = (HTMLInputElement)FindElement(data.passwordField);
                    if (usernameElement == null)
                    {
                        // Utils.l("No es posible encontrar el campo Clave.");
                        return;
                    }
                    else
                    {
                        passwordElement.value = data.password;
                    }


                    try
                    {
                        IHTMLElement submitElement = FindElement(data.submitField);
                        if (submitElement == null)
                        {
                            // Utils.l("No es posible encontrar el elemento Enter.");
                            return;
                        }


                        // try c# click
                        try
                        {
                            submitElement.click();
                        }
                        catch (Exception eee) { }


                        // sleep and the try js click
                        Thread.Sleep(2000);

                        // try js click
                        string prevId = submitElement.id;
                        submitElement.id = "1000Pass_submit_id";


                        IHTMLElementCollection iframes =
                           (IHTMLElementCollection)this.document.getElementsByTagName("iframe");

                        IHTMLElementCollection frames =
                           (IHTMLElementCollection)this.document.getElementsByTagName("frame");

                        if (iframes.length > 0)
                        {
                            foreach (IHTMLElement frm in iframes)
                            {
                                HTMLDocument doc = (HTMLDocument)((SHDocVw.IWebBrowser2)frm).Document;
                                doc.parentWindow.execScript("var element = document.getElementById('1000Pass_submit_id');if(element != null){try{element.click();}catch(e){}}", "javascript");
                            }
                        }
                        else if (frames.length > 0)
                        {
                            foreach (IHTMLElement frm in frames)
                            {
                                HTMLDocument doc = (HTMLDocument)((SHDocVw.IWebBrowser2)frm).Document;
                                doc.parentWindow.execScript("var element = document.getElementById('1000Pass_submit_id');if(element != null){try{element.click();}catch(e){}}", "javascript");
                            }
                        }
                        else
                        {
                            this.document.parentWindow.execScript("var element = document.getElementById('1000Pass_submit_id');if(element != null){try{element.click();}catch(e){}}", "javascript");
                        }


                        // leave the element as it was before
                        submitElement.id = prevId;
                    }
                    catch (Exception eee)
                    {
                        Utils.l(eee);
                    }
                }


                // delete plugin info when finish the binding
                Utils.WriteToFile(Utils.PluginFileName, "");
            }
            catch (Exception e) 
            {
                Utils.l(e);
            }
        }




        IHTMLElement FindElement(string xPath)
        {
            try
            {
                // iframes
                IHTMLElementCollection iframes = this.document.getElementsByTagName("iframe");
                foreach (IHTMLElement iframe in iframes)
                {
                    HTMLFrameElement frm = (HTMLFrameElement)iframe;
                    HTMLDocument doc =(HTMLDocument)((SHDocVw.IWebBrowser2)frm).Document;
                    IHTMLElement theElement = FindElement(xPath, doc);
                    if (theElement != null)
                    {
                        return theElement;
                    }
                }



                // frames
                IHTMLElementCollection frames =
                   (IHTMLElementCollection)this.document.getElementsByTagName("frame");

                if (frames.length > 0)
                {
                    foreach (IHTMLElement frm in frames)
                    {

                        HTMLDocument doc = (HTMLDocument)((SHDocVw.IWebBrowser2)frm).Document;
                        IHTMLElement theElement = FindElement(xPath, doc);
                        if (theElement != null)
                        {
                            return theElement;
                        }
                    }
                }
                else
                {
                    return FindElement(xPath, this.document);
                }
            }
            catch (Exception e)
            {
                Utils.l(e);
            }

            return null;
        }


        IHTMLElement FindElement(string xPath, HTMLDocument doc)
        {
            IHTMLElement theElement = null;

            try
            {
                string[] tmp = xPath.Split(new string[] { "##" }, StringSplitOptions.None);

                string elementAttributes = tmp[1];
                string[] attributes = elementAttributes.Split(new string[] { ";" }, StringSplitOptions.None);
                string attributeId = attributes[0].Replace(@"id=", "");
                string attributeName = attributes[1].Replace(@"name=", "");
                string attributeClass = attributes[2].Replace(@"class=", "");

                string[] xPathParts = tmp[0].Split('/');
                string elementTagName = xPathParts[xPathParts.Length - 1];
                if (elementTagName.Contains("["))
                {
                    elementTagName = elementTagName.Split('[')[0];
                }


                // try in first place the id (if it's unique)
                theElement = doc.getElementById(attributeId);
                if (theElement != null && !String.IsNullOrEmpty(attributeId))
                {
                    int c = 0;
                    IHTMLElementCollection possibleElements = doc.getElementsByTagName(elementTagName);
                    foreach (IHTMLElement possibleElement in possibleElements)
                    {
                        if (possibleElement.id == attributeId)
                        {
                            c++;
                        }
                    }

                    if (c > 1)
                    {
                        theElement = null;
                    }
                }


                if (theElement == null && !String.IsNullOrEmpty(attributeName))
                {
                    IHTMLElementCollection possibleElements = doc.getElementsByName(attributeName);
                    if (possibleElements.length == 1)
                    {
                        theElement = (IHTMLElement)possibleElements.item(null, 0);
                    }
                }


                // try next, the exact xpath
                try
                {
                    if (theElement == null)
                    {
                        IHTMLElementCollection possibleElements = doc.getElementsByTagName(elementTagName);
                        foreach (IHTMLElement possibleElement in possibleElements)
                        {
                            string possibleXPath = "";
                            try
                            {
                                possibleXPath = Utils.FindXPath(possibleElement);
                                //Utils.l(possibleXPath);
                            }
                            catch (Exception e) {
                                //Utils.l(e);
                            }

                            if (possibleXPath == xPath)
                            {
                                theElement = possibleElement;
                                break;
                            }
                        }
                    }
                }
                catch (Exception ex)
                {
                    Utils.l(ex);
                }


                try
                {
                    // next, try the path skipping attributes
                    if (theElement == null)
                    {
                        string cleanXPath = tmp[0];
                        IHTMLElementCollection possibleElements = doc.getElementsByTagName(elementTagName);
                        foreach (IHTMLElement possibleElement in possibleElements)
                        {
                            if (possibleElement.tagName == "INPUT") {
                                IHTMLInputElement tmpInput = (IHTMLInputElement)possibleElement;
                                if (tmpInput.type == "hidden" || tmpInput.type == "text" || tmpInput.type == "password")
                                {
                                    continue;
                                }
                            }

                            string possibleXPath = Utils.FindXPath(possibleElement);
                            string[] possibleTmp = possibleXPath.Split(new string[] { "##" }, StringSplitOptions.None);
                            string cleanPossibleXPath = possibleTmp[0];

                            if (cleanPossibleXPath == cleanXPath)
                            {
                                theElement = possibleElement;
                                break;
                            }
                        }
                    }
                }
                catch (Exception ee)
                {
                    Utils.l(ee);
                }

            }
            catch (Exception e)
            {
                Utils.l(e);
            }

            return theElement;
        }




        void Logo_OnClick(IHTMLEventObj e)
        {
            try
            {
                IHTMLDOMNode plugin = (IHTMLDOMNode)e.srcElement.parentElement.parentElement;

                Data data = new Data(plugin);
                Utils.WriteToFile(Utils.PluginFileName, data.GetAsASingleLine());


                bool alreadyOpened = false;
                if (e.srcElement.getAttribute("alreadyopened", 0).ToString() == "true")
                {
                    alreadyOpened = true;
                }


                // try to find an opened tab with the same url
                int foundAlreadyOpened = -1;
                string toOpenDomain = Utils.GetDomain(data.url);
                List<String> openedUrls = tabs.GetTabUrls((IntPtr)ieInstance.HWND);
                int i = 0;
                foreach (string openedUrl in openedUrls)
                {
                    string openDomain = Utils.GetDomain(openedUrl);
                    if (toOpenDomain.Equals(openDomain))
                    {
                        foundAlreadyOpened = i;
                        break;
                    }
                    i++;
                }

                // when already opened and found the tab index, just activate it.
                if (alreadyOpened)
                {
                    if (foundAlreadyOpened >= 0)
                    {
                        tabs.ActivateTab((IntPtr)ieInstance.HWND, foundAlreadyOpened);
                        return;
                    }
                    else
                    {
                        e.srcElement.setAttribute("alreadyopened", "false", 0);
                    }
                }
                else {
                    // found a tab with same url but not opened, must warm the user before opening a new one
                    if (foundAlreadyOpened >= 0)
                    {
                        //if (System.Windows.Forms.MessageBox.Show("Existe un sitio abierto para " + data.title + ". Desea cerrarlo y continuar?.", "", System.Windows.Forms.MessageBoxButtons.YesNo, System.Windows.Forms.MessageBoxIcon.Question) == System.Windows.Forms.DialogResult.Yes)
                        if (System.Windows.Forms.MessageBox.Show("Existe un sitio abierto para '" + Utils.GetDomain(data.url) + "'. Desea cerrarlo y continuar?.", "", System.Windows.Forms.MessageBoxButtons.YesNo, System.Windows.Forms.MessageBoxIcon.Question) == System.Windows.Forms.DialogResult.Yes)
                        {

                            foreach (IHTMLElement htmlElement in document.getElementsByTagName("IMG"))
                            {
                                if (htmlElement.className == "remote_site_logo")
                                {
                                    IHTMLDOMNode tPlugin = (IHTMLDOMNode)htmlElement.parentElement.parentElement;
                                    Data tData = new Data(tPlugin);
                                    if (Utils.GetDomain(tData.url) == Utils.GetDomain(data.url))
                                    {
                                        htmlElement.setAttribute("alreadyopened", "false", 0);
                                    }
                                }
                            }


                            tabs.ActivateTab((IntPtr)ieInstance.HWND, foundAlreadyOpened);
                            string activeTabText = tabs.GetActiveTabCaption((IntPtr)ieInstance.HWND);
                            tabs.CloseTab((IntPtr)ieInstance.HWND, activeTabText);
                            Thread.Sleep(500);

                        }
                        else 
                        {
                            return;
                        }
                    }
                }


                // deletes cookies for current site before trying the login process (avoid the keep me logging fail)
                try
                {
                    string cachePatternUrl = Cookies.SetupCookieCachePattern(data.url);
                    System.Collections.ArrayList results = Cookies.FindUrlCacheEntries(cachePatternUrl);
                    Cookies.Delete(results);

                    results = Cookies.FindUrlCacheEntries(cachePatternUrl.Replace(@"*www\.", ""));
                    Cookies.Delete(results);

                    results = Cookies.FindUrlCacheEntries(cachePatternUrl.Replace(@"*www\.", "*."));
                    Cookies.Delete(results);


                    /*
                    cachePatternUrl = Cookies.SetupCookieCachePattern(data.url).Replace(@"Cookie:", @"Visited:");
                    results = Cookies.FindUrlCacheEntries(cachePatternUrl);
                    Cookies.Delete(results);

                    results = Cookies.FindUrlCacheEntries(cachePatternUrl.Replace(@"*www\.", ""));
                    Cookies.Delete(results);

                    results = Cookies.FindUrlCacheEntries(cachePatternUrl.Replace(@"*www\.", "*."));
                    Cookies.Delete(results);
                    */
                }
                catch (Exception ee)
                {
                    Utils.l(ee);
                }


                // mark this "logo" as opened
                e.srcElement.setAttribute("alreadyopened", "true", 0);


                object Empty = "";
                object oFlags;
                object urlRedir = "";
                oFlags = 2048; //navOpenInNewTab
                urlRedir = data.url;
                this.ieInstance.Navigate2(ref urlRedir, ref oFlags, ref Empty, ref Empty, ref Empty);

            }
            catch (Exception ex) 
            {
                Utils.l(ex);
            }

        }



        #endregion


        #region dispose

        public void Dispose()
        {
            Dispose(true);
            GC.SuppressFinalize(this);
        }

        protected virtual void Dispose(bool disposing)
        {
            // Protect from being called multiple times.
            if (disposed) return;

            if (disposing)
            {
                // Clean up all managed resources.
                if (openImageDocHostUIHandler != null)
                {
                    openImageDocHostUIHandler.Dispose();
                }
            }
            disposed = true;
        }

        #endregion

    }

}
