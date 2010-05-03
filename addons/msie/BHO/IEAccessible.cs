using System;
using System.Collections.Generic;
using Accessibility;
using System.Runtime.InteropServices;
using System.Diagnostics;
using mshtml;

namespace _1000Pass
{
    public class IEAccessible
    {
        private enum OBJID : uint
        {
            OBJID_WINDOW = 0x00000000,

        }
        private const int IE_ACTIVE_TAB = 2097154;
        private const int CHILDID_SELF = 0;
        private IAccessible accessible;
        private IEAccessible[] Children
        {
            get
            {
                int num = 0;
                object[] res = GetAccessibleChildren(accessible, out num);
                if (res == null)
                    return new IEAccessible[0];

                List<IEAccessible> list = new List<IEAccessible>(res.Length);
                foreach (object obj in res)
                {
                    IAccessible acc = obj as IAccessible;
                    if (acc != null)
                        list.Add(new IEAccessible(acc));
                }
                return list.ToArray();
            }
        }
        private string Name
        {
            get
            {
                string ret = accessible.get_accName(CHILDID_SELF);
                return ret;
            }
        }
        private int ChildCount
        {
            get
            {
                int ret = accessible.accChildCount;
                return ret;
            }
        }

        public IEAccessible()
        {

        }

        public IEAccessible(IntPtr ieHandle, string tabCaptionToActivate)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var ieDirectUIHWND = new IEAccessible(ieHandle);

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                {
                    foreach (var tab in child.Children)
                    {
                        if (tab.Name == tabCaptionToActivate)
                        {
                            tab.Activate();
                            return;
                        }
                    }
                }
            }
        }

        public void ActivateTab(IntPtr ieHandle, string tabCaptionToActivate)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var ieDirectUIHWND = new IEAccessible(ieHandle);

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                {
                    foreach (var tab in child.Children)
                    {
                        if (tab.Name == tabCaptionToActivate)
                        {
                            tab.Activate();
                            return;
                        }
                    }
                }
            }
        }

        public IEAccessible(IntPtr ieHandle, int tabIndexToActivate)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var index = 0;
            var ieDirectUIHWND = new IEAccessible(ieHandle);

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                {
                    foreach (var tab in child.Children)
                    {
                        if (tabIndexToActivate >= child.ChildCount - 1) return;
                        if (index == tabIndexToActivate)
                        {
                            tab.Activate();
                            return;
                        }
                        index++;
                    }
                }
            }
        }
        private IEAccessible(IntPtr ieHandle)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();
        }
        public string GetActiveTabUrl(IntPtr ieHandle)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var ieDirectUIHWND = new IEAccessible(ieHandle);

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                {
                    foreach (var tab in child.Children)
                    {
                        object tabIndex = tab.accessible.get_accState(CHILDID_SELF);

                        if ((int)tabIndex == IE_ACTIVE_TAB)
                        {
                            var description = tab.accessible.get_accDescription(CHILDID_SELF);

                            if (!string.IsNullOrEmpty(description))
                            {
                                if (description.Contains(Environment.NewLine))
                                {
                                    var url = description.Substring(description.IndexOf(Environment.NewLine)).Trim();
                                    return url;
                                }
                            }
                        }
                    }
                }
            }

            return String.Empty;
        }


        public int GetActiveTabIndex(IntPtr ieHandle)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var index = 0;
            var ieDirectUIHWND = new IEAccessible(ieHandle);

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                {
                    foreach (var tab in child.Children)
                    {
                        object tabIndex = tab.accessible.get_accState(0);

                        if ((int)tabIndex == IE_ACTIVE_TAB)
                            return index;

                        index++;
                    }
                }
            }

            return -1;
        }


        public string GetActiveTabCaption(IntPtr ieHandle)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var ieDirectUIHWND = new IEAccessible(ieHandle);

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                {
                    foreach (var tab in child.Children)
                    {
                        object tabIndex = tab.accessible.get_accState(0);

                        if ((int)tabIndex == IE_ACTIVE_TAB)
                            return tab.Name;
                    }
                }
            }
            return String.Empty;
        }
        public List<string> GetTabUrls(IntPtr ieHandle)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var ieDirectUIHWND = new IEAccessible(ieHandle);

            var urlList = new List<string>();

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                    foreach (var tab in child.Children)
                    {
                        var description = tab.accessible.get_accDescription(CHILDID_SELF);

                        if (!string.IsNullOrEmpty(description))
                        {
                            if (description.Contains(Environment.NewLine))
                            {
                                var url = description.Substring(description.IndexOf(Environment.NewLine)).Trim();
                                urlList.Add(url);
                            }
                        }
                    }
            }
            //if (urlList.Count > 0)
            //    urlList.RemoveAt(urlList.Count - 1);

            return urlList;

        }
        public List<string> GetTabCaptions(IntPtr ieHandle)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var ieDirectUIHWND = new IEAccessible(ieHandle);

            var captionList = new List<string>();

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                    foreach (var tab in child.Children)
                        captionList.Add(tab.Name);

            }
            if (captionList.Count > 0)
                captionList.RemoveAt(captionList.Count - 1);

            return captionList;

        }
        public int GetTabCount(IntPtr ieHandle)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var ieDirectUIHWND = new IEAccessible(ieHandle);

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                {
                    foreach (var tab in child.Children)
                        return child.ChildCount - 1;
                }
            }
            return 0;

        }

        private IntPtr GetDirectUIHWND(IntPtr ieFrame)
        {
            var directUI = FindWindowEx(ieFrame, IntPtr.Zero, "CommandBarClass", null);
            directUI = FindWindowEx(directUI, IntPtr.Zero, "ReBarWindow32", null);
            directUI = FindWindowEx(directUI, IntPtr.Zero, "TabBandClass", null);
            directUI = FindWindowEx(directUI, IntPtr.Zero, "DirectUIHWND", null);
            return directUI;

        }
        private IEAccessible(IAccessible acc)
        {
            if (acc == null)
                throw new Exception();

            accessible = acc;
        }
        private void Activate()
        {
            accessible.accDoDefaultAction(CHILDID_SELF);
        }
        private void Navigate()
        {

        }
        private static object[] GetAccessibleChildren(IAccessible ao, out int childs)
        {
            childs = 0;
            object[] ret = null;
            int count = ao.accChildCount;
            if (count > 0)
            {
                ret = new object[count];
                AccessibleChildren(ao, 0, count, ret, out childs);
            }
            return ret;
        }


        #region Interop
        [DllImport("user32.dll", SetLastError = true)]
        private static extern IntPtr FindWindowEx(IntPtr hwndParent, IntPtr hwndChildAfter, string lpszClass,
        string lpszWindow);
        private static int AccessibleObjectFromWindow(IntPtr hwnd, OBJID idObject, ref IAccessible acc)
        {
            Guid guid = new Guid("{618736e0-3c3d-11cf-810c-00aa00389b71}"); // IAccessible

            object obj = null;
            int num = AccessibleObjectFromWindow(hwnd, (uint)idObject, ref guid, ref obj);
            acc = (IAccessible)obj;
            return num;
        }
        [DllImport("oleacc.dll")]
        private static extern int AccessibleObjectFromWindow(IntPtr hwnd, uint id, ref Guid iid, [In, Out, MarshalAs(UnmanagedType.IUnknown)] ref object ppvObject);
        [DllImport("oleacc.dll")]
        private static extern int AccessibleChildren(IAccessible paccContainer, int iChildStart, int cChildren, [In, Out, MarshalAs(UnmanagedType.LPArray, SizeParamIndex = 2)] object[] rgvarChildren, out int pcObtained);
        #endregion


        public void IEAccessibleCloseTab(IntPtr ieHandle, string tabCaptionToClose)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);

            if (accessible == null)
                throw new Exception();

            var ieDirectUIHWND = new IEAccessible(ieHandle);

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                {
                    foreach (var tab in child.Children)
                    {
                        if (tab.Name == tabCaptionToClose)
                        {
                            foreach (var CloseTab in tab.Children)
                                CloseTab.Activate();

                            return;
                        }
                    }
                }
            }
        }

        public void CloseTab(IntPtr ieHandle, string tabCaptionToClose)
        {
            AccessibleObjectFromWindow(GetDirectUIHWND(ieHandle), OBJID.OBJID_WINDOW, ref accessible);
            if (accessible == null)
                throw new Exception();

            var ieDirectUIHWND = new IEAccessible(ieHandle);

            foreach (IEAccessible accessor in ieDirectUIHWND.Children)
            {
                foreach (var child in accessor.Children)
                {
                    foreach (var tab in child.Children)
                    {
                        if (tab.Name == tabCaptionToClose)
                        {
                            foreach (var CloseTab in tab.Children)
                                CloseTab.Activate();
                            return;
                        }
                    }
                }
            }
        }


        [DllImport("user32.dll", SetLastError = true, CharSet = CharSet.Auto)]
        public static extern uint RegisterWindowMessage(string lpString);
        [DllImport("oleacc.dll", PreserveSig = false)]
        [return: MarshalAs(UnmanagedType.Interface)]
        public static extern object ObjectFromLresult(UIntPtr lResult,
        [MarshalAs(UnmanagedType.LPStruct)] Guid refiid, IntPtr wParam);
        [DllImport("user32.dll", SetLastError = true, CharSet = CharSet.Auto)]
        public static extern IntPtr SendMessageTimeout(
            IntPtr hWnd,
            uint Msg,
            UIntPtr wParam,
            UIntPtr lParam,
            SendMessageTimeoutFlags fuFlags,
            uint uTimeout,
            out UIntPtr lpdwResult);

        [Flags]
        public enum SendMessageTimeoutFlags : uint
        {
            SMTO_NORMAL = 0x0000,
            SMTO_BLOCK = 0x0001,
            SMTO_ABORTIFHUNG = 0x0002,
            SMTO_NOTIMEOUTIFNOTHUNG = 0x0008
        }

        public IHTMLDocument2 GetIEDocumentFromHwnd(IntPtr ieServerHwnd)
        {
            UIntPtr lResult;
            IHTMLDocument2 htmlDocument = null;

            if (ieServerHwnd != IntPtr.Zero)
            {

                var lMsg = RegisterWindowMessage("WM_HTML_GETOBJECT");

                SendMessageTimeout(ieServerHwnd, lMsg, UIntPtr.Zero, UIntPtr.Zero,

                SendMessageTimeoutFlags.SMTO_ABORTIFHUNG, 1000, out lResult);
                if (lResult != UIntPtr.Zero)
                {

                    htmlDocument = ObjectFromLresult(lResult,
                    typeof(IHTMLDocument).GUID, IntPtr.Zero) as IHTMLDocument2;
                    if (htmlDocument == null)
                    {
                        throw new COMException("Unable to cast the object");

                    }
                }
            }

            return htmlDocument;
        }
    }
}