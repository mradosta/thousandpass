/****************************** Module Header ******************************\
* Module Name:  OpenImageHandler.cs
* Project:      CSCustomIEContextMenu
* Copyright (c) Microsoft Corporation.
* 
* The class OpenImageHandler implements the interface IDocHostUIHandler.ShowContextMenu
* method. For other methods in the interface IDocHostUIHandler, just return 1 which means
* the default handler will be used.
*  
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
using System.Drawing;
using System.Windows.Forms;
using _1000Pass_com.NativeMethods;
using mshtml;
using SHDocVw;

namespace _1000Pass_com
{
    class OpenMenuHandler : NativeMethods.IDocHostUIHandler, IDisposable
    {

        const string USERNAME = @"Usuario";
        const string PASSWORD = @"Clave";
        const string ENTER = @"Entrar";

        const string EMPTY_USERNAME = @"Debe ingresar su usuario antes de agregar el campo usuario a 1000pass.com";
        const string EMPTY_PASSWORD = @"Debe ingresar su clave antes de agregar el campo clave a 1000pass.com";


        private mshtml.IHTMLElement clickedElement;

        private bool adding = false;

        private string usernameValue = "";
        private string username = "";
        private string passwordValue = "";
        private string password = "";
        private string enter = "";

        private bool disposed = false;

        // The IE instance that hosts this WebBrowser Control.
        public InternetExplorer ieInstance;

        // The custom context menu. 
        private ContextMenuStrip contextMenu;
        private ToolStripMenuItem menuItemUsername;
        private ToolStripMenuItem menuItemPassword;
        private ToolStripMenuItem menuItemEnter;
        private ToolStripMenuItem menuItemAddManual;
        private ToolStripMenuItem menuItemAddAuto;
        private ToolStripMenuItem menuItemCancel;



        public OpenMenuHandler(InternetExplorer host)
        {
            this.ieInstance = host;

            contextMenu = new ContextMenuStrip();

            menuItemAddAuto = new ToolStripMenuItem();
            menuItemAddAuto.Size = new Size(180, 100);
            menuItemAddAuto.Text = "Agregar a 1000Pass.com (Automatico)";
            menuItemAddAuto.Click += new EventHandler(AddAutoHandler);
            contextMenu.Items.Add(menuItemAddAuto);

            menuItemAddManual = new ToolStripMenuItem();
            menuItemAddManual.Size = new Size(180, 100);
            menuItemAddManual.Text = "Agregar a 1000Pass.com (Manual)";
            menuItemAddManual.Click += new EventHandler(AddManualHandler);
            contextMenu.Items.Add(menuItemAddManual);

            menuItemUsername = new ToolStripMenuItem();
            menuItemUsername.Size = new Size(180, 100);
            menuItemUsername.Text = "Agregar campo de usuario";
            menuItemUsername.Click += new EventHandler(UsernameHandler);
            contextMenu.Items.Add(menuItemUsername);

            menuItemPassword = new ToolStripMenuItem();
            menuItemPassword.Size = new Size(180, 100);
            menuItemPassword.Text = "Agregar campo de clave";
            menuItemPassword.Click += new EventHandler(PasswordHandler);
            contextMenu.Items.Add(menuItemPassword);

            menuItemEnter = new ToolStripMenuItem();
            menuItemEnter.Size = new Size(180, 100);
            menuItemEnter.Text = "Agregar boton de ingresar";
            menuItemEnter.Click += new EventHandler(EnterHandler);
            contextMenu.Items.Add(menuItemEnter);

            menuItemCancel = new ToolStripMenuItem();
            menuItemCancel.Size = new Size(180, 100);
            menuItemCancel.Text = "Cancelar agregado a 1000Pass.com";
            menuItemCancel.Click += new EventHandler(CancelHandler);
            contextMenu.Items.Add(menuItemCancel);

        }


        void AddAutoHandler(object sender, EventArgs e)
        {
            DialogResult dr = System.Windows.Forms.MessageBox.Show("Para poder agregar el nuevo sitio a 1000Pass.com es necesario que haya ingresado su usuario y clave. Ya lo ha hecho?", "1000pass.com", MessageBoxButtons.YesNo);

            if (dr == System.Windows.Forms.DialogResult.Yes)
            {

                IHTMLDocument3 htmlDoc = (IHTMLDocument3)ieInstance.Document;
                IHTMLElementCollection possibleElements = htmlDoc.getElementsByTagName("INPUT");
                IHTMLElement passwordElement = null;
                IHTMLElement usernameElement = null;
                IHTMLElement enterElement = null;
                int c = 0;
                foreach (IHTMLElement possibleElement in possibleElements)
                {
                    // Only not empty input type password
                    if (possibleElement.getAttribute("type").ToString().ToUpper() == "PASSWORD"
                        && !String.IsNullOrEmpty(((IHTMLInputElement)possibleElement).value))
                    {
                        c++;
                        passwordElement = possibleElement;
                    }
                }


                if (c == 1)
                {
                    int cc = 0;
                    IHTMLFormElement form = ((IHTMLInputElement)passwordElement).form;
                    if (form != null)
                    {
                        foreach (IHTMLElement possibleElement in possibleElements)
                        {
                            string type = possibleElement.getAttribute("type").ToString().ToUpper();
                            if (type == "TEXT" || type == "SUBMIT")
                            {
                                IHTMLFormElement possibleForm = ((IHTMLInputElement)possibleElement).form;
                                if (possibleForm.Equals(form))
                                {
                                    if (type == "TEXT")
                                    {
                                        usernameElement = possibleElement;
                                        cc++;
                                    }
                                    else if (type == "SUBMIT")
                                    {
                                        enterElement = possibleElement;
                                        cc++;
                                    }
                                }
                            }
                        }

                        // Enter element may be a button type submit too! 
                        if (cc == 1 && enterElement == null)
                        {
                            possibleElements = htmlDoc.getElementsByTagName("BUTTON");
                            foreach (IHTMLElement possibleElement in possibleElements)
                            {
                                string type = possibleElement.getAttribute("type").ToString().ToUpper();
                                if (type == "SUBMIT")
                                {
                                    if (((IHTMLButtonElement)possibleElement).form.Equals(form))
                                    {
                                        enterElement = possibleElement;
                                        cc++;
                                    }
                                }
                            }
                        }


                        if (cc == 2
                            && passwordElement != null
                            && usernameElement != null
                            && enterElement != null)
                        {

                            this.usernameValue = ((IHTMLInputElement)usernameElement).value;
                            if (String.IsNullOrEmpty(this.usernameValue))
                            {
                                Utils.l(EMPTY_USERNAME);
                                return;
                            }
                            else
                            {
                                this.clickedElement = usernameElement;
                                this.username = Utils.FindXPath(usernameElement);
                                MarkAsSelected(USERNAME);
                            }


                            this.passwordValue = ((IHTMLInputElement)passwordElement).value;
                            if (String.IsNullOrEmpty(this.passwordValue))
                            {
                                Utils.l(EMPTY_PASSWORD);
                                return;
                            }
                            else
                            {
                                this.clickedElement = passwordElement;
                                this.password = Utils.FindXPath(passwordElement);
                                MarkAsSelected(PASSWORD);
                            }


                            this.clickedElement = enterElement;
                            this.enter = Utils.FindXPath(enterElement);
                            MarkAsSelected(ENTER);

                            Send();
                        }
                        else
                        {
                            this.adding = true;

                            this.menuItemAddManual.Visible = false;
                            this.menuItemAddAuto.Visible = false;

                            this.menuItemUsername.Visible = false;
                            this.menuItemPassword.Visible = false;
                            this.menuItemEnter.Visible = false;

                            if (usernameElement == null)
                            {
                                this.menuItemUsername.Enabled = true;
                                this.menuItemUsername.Visible = true;
                            }
                            else
                            {
                                this.clickedElement = usernameElement;
                                this.username = Utils.FindXPath(usernameElement);
                                this.usernameValue = ((IHTMLInputElement)usernameElement).value;
                                MarkAsSelected(USERNAME);
                            }


                            if (passwordElement == null)
                            {
                                this.menuItemPassword.Enabled = true;
                                this.menuItemPassword.Visible = true;
                            }
                            else
                            {
                                this.clickedElement = passwordElement;
                                this.password = Utils.FindXPath(passwordElement);
                                this.passwordValue = ((IHTMLInputElement)passwordElement).value;
                                MarkAsSelected(PASSWORD);
                            }


                            if (enterElement == null)
                            {
                                this.menuItemEnter.Enabled = true;
                                this.menuItemEnter.Visible = true;
                            }
                            else
                            {
                                this.clickedElement = enterElement;
                                this.enter = Utils.FindXPath(enterElement);
                                MarkAsSelected(ENTER);
                            }


                            Utils.l(@"No podemos encontrar todos los campo en forma automatica. Por favor, agregue los campos restantes en manualmente presionando el boton derecho sobre los elementos que no se pudieron encontrar.");
                        }
                    } // form == null
                }
                else
                {
                    Utils.l(@"Existe mas de un campo de tipo clave en esta pagina. Por favor, agregue los campos en forma manual.");
                }
            }
        }
        

        void AddManualHandler(object sender, EventArgs e)
        {
            this.adding = true;
            this.menuItemAddManual.Visible = false;
            this.menuItemAddAuto.Visible = false;
            this.menuItemUsername.Visible = true;
            this.menuItemPassword.Visible = true;
            this.menuItemEnter.Visible = true;
            this.menuItemCancel.Visible = true;

            this.menuItemUsername.Enabled = true;
            this.menuItemPassword.Enabled = true;
            this.menuItemEnter.Enabled = true;
            this.menuItemCancel.Enabled = true;

            Utils.l(@"A continuacion, haga click con el boton derecho del raton sobre el campo usuario, el campo clave y el boton ingresar");
        }

        void CancelHandler(object sender, EventArgs e)
        {
            DialogResult dr = System.Windows.Forms.MessageBox.Show("Esta seguro que desea cancelar el agregar el sitio a 1000pass.com?", "1000pass.com", MessageBoxButtons.YesNo);
            if (dr == System.Windows.Forms.DialogResult.Yes)
            {
                this.adding = false;
                this.menuItemAddManual.Visible = true;
                this.menuItemAddAuto.Visible = true;
                this.menuItemUsername.Visible = false;
                this.menuItemPassword.Visible = false;
                this.menuItemEnter.Visible = false;
                this.menuItemCancel.Visible = false;
            }

            // removes borders... (and others?)
            ieInstance.Refresh();
        }


        void MarkAsSelected(string text)
        {
            try
            {
                HTMLDocument document = (HTMLDocument)ieInstance.Document;
                HTMLHeadElement head = (HTMLHeadElement)((IHTMLElementCollection)document.all.tags("head")).item(null, 0);
                string bubble = @"<p class='1000pass_bubble' style='padding:3px 0 0 0;background: transparent url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAAAUCAYAAAAN+ioeAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9sIGhc6EnSAoLIAAAAZdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAACCUlEQVRYw+3Xv09TURjG8e9zaUVFC8RqQ0pcMDJBQghhIEyExQ0G/wiZXPwTnFhbZxlxcHIwDoaxM5ogi2I00URCcknKj17v44ANcNOAwHXqeZezvcPnvHnOe0SOdbj5er71YWU5jbfG3fyJEF1ZNo6KDdLWqqS67f3cJPbeP3uUfH77xs0foB6LqHudwbJlCewNYKInj8at7c3B5OPKC8df7ks9SOpaZACBOEK2pLvATiGPxgfvlq453rppG6mLhbPgkrAhKj7OBTr9tQ5Hkxx0szEimbQ1HV22QVyrHt9cVIiBJLB2jpELJelufZjbT75lsReAUWBR0lSIjjPBzwWu2L4FDEiaAGZtzwAjbdS/wP6XfgH6eErnJJVtT0sq2Z6UNARU2hMbYC8BHdeqT4F7khZt90nqBwq2r2diIMBeoaLjz4xPne3JDfWfomO3PjxnuwxMAyVgUtKQ7crJTD7aXMLLl/tjKGnA9gQwC8wAI5nlPETLVaBP7s2lpe/ZS1gARm0vAlPtyAl1Beiz4ONa9QawJmkqkJ7zGF60Tk23f5eAQuDs8AUHY18e+tRtlcdQsS9sKZ0iw5ajQiMX6N752mE0+KAZ8jkzzbYtQZqs5gJdvPNwJyqPPae3H6cJduoQF25/RjaAeq4jeLD+cj759Go5jb+Oe2+7ezcQG6JCgzRZNdQl7f8BgazjmglumqIAAAAASUVORK5CYII=) no-repeat;margin-left:70px;width:90px;height:29px;position:absolute;font-size:14px;text-align:center;font-weight:bold;color:#eeeeee;'>" + text + "</p>";
                this.clickedElement.insertAdjacentHTML("beforebegin", bubble);
                string className = this.clickedElement.className + " 1000pass_selected";

                this.clickedElement.className = className.Trim();
            }
            catch (Exception e)
            {
                Utils.l(e);
            }
            
        }


        void UsernameHandler(object sender, EventArgs e)
        {
            this.usernameValue = ((IHTMLInputElement)this.clickedElement).value;
            if (String.IsNullOrEmpty(this.usernameValue))
            {
                Utils.l(EMPTY_USERNAME);
            }
            else
            {
                this.menuItemUsername.Enabled = false;
                this.username = Utils.FindXPath(this.clickedElement);
                MarkAsSelected(USERNAME);
                Send();
            }
        }


        void PasswordHandler(object sender, EventArgs e)
        {
            this.passwordValue = ((IHTMLInputElement)this.clickedElement).value;
            if (String.IsNullOrEmpty(this.passwordValue))
            {
                Utils.l(EMPTY_PASSWORD);
            }
            else
            {
                this.menuItemPassword.Enabled = false;
                this.password = Utils.FindXPath(this.clickedElement);
                MarkAsSelected(PASSWORD);
                Send();
            }
        }


        void EnterHandler(object sender, EventArgs e)
        {
            this.menuItemEnter.Enabled = false;
            this.enter = Utils.FindXPath(this.clickedElement);
            MarkAsSelected(ENTER);
            Send();
        }


        void Send() {

            if (this.username != "" && this.password != "" && this.enter != "") {

                string token = Utils.ReadFromFile(Utils.TokenFileName);
                if (!String.IsNullOrEmpty(token))
                {
                    DialogResult dr = System.Windows.Forms.MessageBox.Show("Esta seguro que desea agregar el sitio a 1000pass.com?", "1000pass.com", MessageBoxButtons.OKCancel);

                    if (dr == System.Windows.Forms.DialogResult.OK)
                    {
                        Utils.l(Utils.AddTo1000Pass(token, 
                            ((HTMLDocument) this.ieInstance.Document).url,
                            ((HTMLDocument) this.ieInstance.Document).title,
                            this.username, this.usernameValue,
                            this.password, this.passwordValue,
                            this.enter));
                    }

                    this.adding = false;

                    this.username = "";
                    this.password = "";
                    this.enter = "";

                    this.menuItemAddManual.Visible = true;
                    this.menuItemAddAuto.Visible = true;

                    // removes borders... (and others?)
                    ieInstance.Refresh();

                }
                else 
                {
                    Utils.l("Debe ingresar a su cuenta de 1000pass.com antes de agregar el nuevo sitio");
                }
            }
        }





        #region IDocHostUIHandler

        /// <summary>
        /// Show custom Context Menu for Image.
        /// </summary>
        /// <param name="dwID">
        /// A DWORD that specifies the identifier of the shortcut menu to be displayed. 
        /// See NativeMethods.CONTEXT_MENU_CONST.
        /// </param>
        /// <param name="pt">
        /// The screen coordinates for the menu.
        /// </param>
        /// <param name="pcmdtReserved"></param>
        /// <param name="pdispReserved">
        /// The object at the screen coordinates specified in ppt. This enables a host to
        /// pass particular objects, such as anchor tags and images, to provide more 
        /// specific context.
        /// </param>
        /// <returns>
        /// Return 0 means that host displayed its UI. MSHTML will not attempt to display its UI. 
        /// </returns>

        public int ShowContextMenu(int dwID, POINT pt, object pcmdtReserved, object pdispReserved)
        {
            try
            {
                this.clickedElement = pdispReserved as mshtml.IHTMLElement;

                if (this.adding)
                {
                    this.menuItemUsername.Visible = true;
                    this.menuItemPassword.Visible = true;
                    this.menuItemEnter.Visible = true;

                    string type = "";
                    try 
                    {
                        type = this.clickedElement.getAttribute("type").ToString();
                    }
                    catch (Exception ee) {}

                    if (type.ToUpper() == "TEXT")
                    {
                        this.menuItemPassword.Visible = false;
                        this.menuItemEnter.Visible = false;
                    }
                    else if (type.ToUpper() == "PASSWORD")
                    {
                        this.menuItemUsername.Visible = false;
                        this.menuItemEnter.Visible = false;
                    }
                    else
                    {
                        this.menuItemUsername.Visible = false;
                        this.menuItemPassword.Visible = false;
                        this.menuItemEnter.Visible = true;
                        this.menuItemCancel.Visible = true;
                    }

                    contextMenu.Show(pt.x, pt.y);

                    return 0;
                }
                else if (!this.adding)
                {
                    contextMenu.Show(pt.x + 2, pt.y - 47);

                    this.menuItemUsername.Visible = false;
                    this.menuItemPassword.Visible = false;
                    this.menuItemEnter.Visible = false;
                    this.menuItemCancel.Visible = false;

                    return 1;

                }
                else
                {
                    return 1;
                }
            }
            catch (Exception e)
            {
                Utils.l(e);
                return 1;
            }

        }

        public int GetHostInfo(DOCHOSTUIINFO info)
        {
            return 1;
        }

        public int ShowUI(int dwID, IOleInPlaceActiveObject activeObject, IOleCommandTarget commandTarget, IOleInPlaceFrame frame, IOleInPlaceUIWindow doc)
        {
            return 1;
        }

        public int HideUI()
        {
            return 1;
        }

        public int UpdateUI()
        {
            return 1;
        }

        public int EnableModeless(bool fEnable)
        {
            return 1;
        }

        public int OnDocWindowActivate(bool fActivate)
        {
            return 1;
        }

        public int OnFrameWindowActivate(bool fActivate)
        {
            return 1;
        }

        public int ResizeBorder(COMRECT rect, IOleInPlaceUIWindow doc, bool fFrameWindow)
        {
            return 1;
        }

        public int TranslateAccelerator(ref MSG msg, ref Guid group, int nCmdID)
        {
            return 1;
        }

        public int GetOptionKeyPath(string[] pbstrKey, int dw)
        {
            return 1;
        }

        public int GetDropTarget(IOleDropTarget pDropTarget, out IOleDropTarget ppDropTarget)
        {
            ppDropTarget = null;
            return 1;
        }

        public int GetExternal(out object ppDispatch)
        {
            ppDispatch = null;
            return 1;
        }

        public int TranslateUrl(int dwTranslate, string strURLIn, out string pstrURLOut)
        {
            pstrURLOut = string.Empty;
            return 1;
        }

        public int FilterDataObject(System.Runtime.InteropServices.ComTypes.IDataObject pDO, out System.Runtime.InteropServices.ComTypes.IDataObject ppDORet)
        {
            ppDORet = null;
            return 1;
        }
        #endregion


        #region Dispose
        public void Dispose()
        {
            Dispose(true);
            GC.SuppressFinalize(this);
        }

        protected virtual void Dispose(bool disposing)
        {
            // Protect from being called multiple times.
            if (this.disposed) return;

            if (disposing)
            {
                // Clean up all managed resources.
                if (this.contextMenu != null)
                {
                    this.contextMenu.Dispose();
                }

                if (this.menuItemUsername != null)
                {
                    this.menuItemUsername.Dispose();
                }
                if (this.menuItemPassword != null)
                {
                    this.menuItemPassword.Dispose();
                }
                if (this.menuItemEnter != null)
                {
                    this.menuItemEnter.Dispose();
                }
                if (this.menuItemAddManual != null)
                {
                    this.menuItemAddManual.Dispose();
                }
                if (this.menuItemAddAuto != null)
                {
                    this.menuItemAddAuto.Dispose();
                }
                if (this.menuItemCancel != null)
                {
                    this.menuItemCancel.Dispose();
                }

            }
            this.disposed = true;
        }
        #endregion
    }
}
