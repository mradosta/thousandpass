using System;
using System.Collections.Generic;
using System.Text;
using mshtml;

namespace _1000Pass_com
{
    public class Data
    {
        public string plugin_identifier { get; set; }
        public string title { get; set; }
        public string url { get; set; }
        public string username { get; set; }
        public string usernameField { get; set; }
        public string password { get; set; }
        public string extra { get; set; }
        public string extraField { get; set; }
        public string passwordField { get; set; }
        public string submitField { get; set; }


        public Data(IHTMLDOMNode pluginNode)
        {
            IHTMLDOMNode divHidden = pluginNode.firstChild.nextSibling.nextSibling.nextSibling;

            IHTMLDOMNode divPluginIdentifier = divHidden.firstChild;
            IHTMLDOMNode divTitle = divPluginIdentifier.nextSibling;
            IHTMLDOMNode divUrl = divTitle.nextSibling;
            IHTMLDOMNode divUsername = divUrl.nextSibling;
            IHTMLDOMNode divPassword = divUsername.nextSibling;
            IHTMLDOMNode divExtra = divPassword.nextSibling;
            IHTMLDOMNode divSubmit = divExtra.nextSibling;


            this.plugin_identifier = Utils.GetInnerText(divPluginIdentifier);
            this.title = Utils.GetInnerText(divTitle);
            this.url = Utils.GetInnerText(divUrl).Replace("&amp;", "&");
            this.username = Utils.GetInnerText(divUsername);
            this.password = Utils.decode64(Utils.GetInnerText(divPassword));
            this.extra = Utils.GetInnerText(divExtra);
            this.usernameField = Utils.GetClassName(divUsername);
            this.passwordField = Utils.GetClassName(divPassword);
            this.extraField = Utils.GetClassName(divExtra);
            this.submitField = Utils.GetClassName(divSubmit);

        }


        public string GetAsASingleLine()
        {
            StringBuilder sb = new StringBuilder();
            sb.Append(this.plugin_identifier);
            sb.Append("#*#");
            sb.Append(this.title);
            sb.Append("#*#");
            sb.Append(this.url);
            sb.Append("#*#");
            sb.Append(this.username);
            sb.Append("#*#");
            sb.Append(this.password);
            sb.Append("#*#");
            sb.Append(this.extra);
            sb.Append("#*#");
            sb.Append(this.usernameField);
            sb.Append("#*#");
            sb.Append(this.passwordField);
            sb.Append("#*#");
            sb.Append(this.extraField);
            sb.Append("#*#");
            sb.Append(this.submitField);
            sb.Append("#*#");

            return sb.ToString();
        }

        public Data(string line)
        {
            string[] options = line.Split(new string[] { "#*#" }, StringSplitOptions.None);

            this.plugin_identifier = options[0];
            this.title = options[1];
            this.url = options[2];
            this.username = options[3];
            this.password = options[4];
            this.extra = options[5];
            this.usernameField = options[6];
            this.passwordField = options[7];
            this.extraField = options[8];
            this.submitField = options[9];
        }
    }
}