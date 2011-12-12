using System;
using System.Collections;
using System.Collections.Generic;
using System.Text;

namespace _1000Pass_com
{
    class XPath
    {
        public static string getXPath(mshtml.IHTMLElement element)
        {
            if (element == null)
                return "";
            mshtml.IHTMLElement currentNode = element;
            ArrayList path = new ArrayList();

            while (currentNode != null)
            {
                string pe = getNode(currentNode);
                if (pe != null)
                {
                    path.Add(pe);
                    //if (pe.IndexOf("@id") != -1)
                    //    break;  // Found an ID, no need to go upper, absolute path is OK
                }
                currentNode = currentNode.parentElement;
            }
            path.Reverse();
            return join(path, "/");
        }

        private static string join(ArrayList items, string delimiter)
        {
            StringBuilder sb = new StringBuilder();
            foreach (object item in items)
            {
                if (item == null)
                    continue;

                sb.Append(delimiter);
                sb.Append(item);
            }
            return sb.ToString();
        }

        private static string getNode(mshtml.IHTMLElement node)
        {
            string nodeExpr = node.tagName;
            if (nodeExpr == null)  // Eg. node = #text
                return null;
            if (node.id != "" && node.id != null)
            {
                nodeExpr += "[@id='" + node.id + "']";
                // We don't really need to go back up to //HTML, since IDs are supposed
                // to be unique, so they are a good starting point.
                return "/" + nodeExpr;
            }

            // Find rank of node among its type in the parent
            int rank = 1;
            mshtml.IHTMLDOMNode nodeDom = node as mshtml.IHTMLDOMNode;
            mshtml.IHTMLDOMNode psDom = nodeDom.previousSibling;
            mshtml.IHTMLElement ps = psDom as mshtml.IHTMLElement;
            while (ps != null)
            {
                if (ps.tagName == node.tagName)
                {
                    rank++;
                }
                psDom = psDom.previousSibling;
                ps = psDom as mshtml.IHTMLElement;
            }
            if (rank > 1)
            {
                nodeExpr += "[" + rank + "]";
            }
            else
            { // First node of its kind at this level. Are there any others?
                mshtml.IHTMLDOMNode nsDom = nodeDom.nextSibling;
                mshtml.IHTMLElement ns = nsDom as mshtml.IHTMLElement;
                while (ns != null)
                {
                    if (ns.tagName == node.tagName)
                    { // Yes, mark it as being the first one
                        nodeExpr += "[1]";
                        break;
                    }
                    nsDom = nsDom.nextSibling;
                    ns = nsDom as mshtml.IHTMLElement;
                }
            }
            return nodeExpr;
        }
    }
}