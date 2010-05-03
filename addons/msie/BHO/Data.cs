using System;
using System.Collections.Generic;
using System.Text;

namespace _1000Pass
{
    public class Data
    {
        public string plugin_identifier { get; set; }
        public string title { get; set; }
        public string url { get; set; }
        public string username { get; set; }
        public string usernameField { get; set; }
        public string password { get; set; }
        public string passwordField { get; set; }
        public string submit { get; set; }
        public string logout { get; set; }
        public string logoutMethod { get; set; }


        public Data(string line)
        {
            string[] options = line.Split(new string[] { "#*#" }, StringSplitOptions.None);

            this.plugin_identifier = options[0];
            this.title = options[1];
            this.url = options[2];
            this.username = options[3];
            this.password = options[4];
            this.usernameField = options[5];
            this.passwordField = options[6];
            this.submit = options[7];
            this.logout = options[8];
            this.logoutMethod = options[9];
        }
    }

}
