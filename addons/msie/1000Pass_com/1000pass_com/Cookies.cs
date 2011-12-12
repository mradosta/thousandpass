using System;
using System.Collections.Generic;
using System.Text;
using System.Runtime.InteropServices;
using System.Net;
using System.Collections;
using System.Text.RegularExpressions;

namespace _1000Pass_com
{
    public class Cookies
    {

        [StructLayout(LayoutKind.Sequential)]
        public struct INTERNET_CACHE_ENTRY_INFO
        {
            public UInt32 dwStructSize;
            public string lpszSourceUrlName;
            public string lpszLocalFileName;
            public UInt32 CacheEntryType;
            public UInt32 dwUseCount;
            public UInt32 dwHitRate;
            public UInt32 dwSizeLow;
            public UInt32 dwSizeHigh;
            public FILETIME LastModifiedTime;
            public FILETIME ExpireTime;
            public FILETIME LastAccessTime;
            public FILETIME LastSyncTime;
            public IntPtr lpHeaderInfo;
            public UInt32 dwHeaderInfoSize;
            public string lpszFileExtension;
            public UInt32 dwExemptDelta;
        };

        

        //Wininet
        public const int ERROR_SUCCESS = 0;
        public const int ERROR_FILE_NOT_FOUND = 2;
        public const int ERROR_ACCESS_DENIED = 5;
        public const int ERROR_INSUFFICIENT_BUFFER = 122;
        public const int ERROR_NO_MORE_ITEMS = 259;


        [DllImport("wininet.dll", SetLastError = true)]
        public static extern IntPtr FindFirstUrlCacheEntry(string lpszUrlSearchPattern, IntPtr lpFirstCacheEntryInfo, out UInt32 lpdwFirstCacheEntryInfoBufferSize);

        [DllImport("wininet.dll", SetLastError = true)]
        public static extern long FindNextUrlCacheEntry(IntPtr hEnumHandle, IntPtr lpNextCacheEntryInfo, out UInt32 lpdwNextCacheEntryInfoBufferSize);

        [DllImport("wininet.dll", SetLastError = true)]
        public static extern long FindCloseUrlCache(IntPtr hEnumHandle);

        [DllImport("wininet.dll", SetLastError = true)]
        public static extern long DeleteUrlCacheEntry(string lpszUrlName);


        public static void Delete(ArrayList results)
        {
            try
            {
                foreach (INTERNET_CACHE_ENTRY_INFO entry in results)
                {
                    DeleteUrlCacheEntry(entry.lpszSourceUrlName);
                }
            }
            catch (Exception e)
            {
                Utils.l(e);
            }
        }


        /// <summary>
        /// UrlCache functionality is taken from:
        /// Scott McMaster (smcmaste@hotmail.com)
        /// CodeProject article
        /// 
        /// There were some issues with preparing URLs
        /// for RegExp to work properly. This is
        /// demonstrated in AllForms.SetupCookieCachePattern method
        /// 
        /// urlPattern:
        /// . Dump the entire contents of the cache.
        /// Cookie: Lists all cookies on the system.
        /// Visited: Lists all of the history items.
        /// Cookie:.*\.example\.com Lists cookies from the example.com domain.
        /// http://www.example.com/example.html$: Lists the specific named file if present
        /// \.example\.com: Lists any and all entries from *.example.com.
        /// \.example\.com.*\.gif$: Lists the .gif files from *.example.com.
        /// \.js$: Lists the .js files in the cache.
        /// </summary>
        /// <param name="urlPattern"></param>
        /// <returns></returns>
        public static ArrayList FindUrlCacheEntries(string urlPattern)
        {
        
            ArrayList results = new ArrayList();
            IntPtr buffer = IntPtr.Zero;
            UInt32 structSize;

        
            //This call will fail but returns the size required in structSize
            //to allocate necessary buffer
            IntPtr hEnum = FindFirstUrlCacheEntry(null, buffer, out structSize);
            try
            {
                if (hEnum == IntPtr.Zero)
                {
                    int lastError = Marshal.GetLastWin32Error();
                    if (lastError == ERROR_INSUFFICIENT_BUFFER)
                    {
                        //Allocate buffer
                        buffer = Marshal.AllocHGlobal((int)structSize);
                        //Call again, this time it should succeed
                        hEnum = FindFirstUrlCacheEntry(urlPattern, buffer, out structSize);
                    }
                    else if (lastError == ERROR_NO_MORE_ITEMS)
                    {
                        return results;
                    }
                }

            
                INTERNET_CACHE_ENTRY_INFO result = (INTERNET_CACHE_ENTRY_INFO)Marshal.PtrToStructure(buffer, typeof(INTERNET_CACHE_ENTRY_INFO));
                try
                {
                    if (Regex.IsMatch(result.lpszSourceUrlName, urlPattern, RegexOptions.IgnoreCase))
                    {
                        results.Add(result);
                    }
                }
                catch (ArgumentException ae)
                {
                    throw new ApplicationException("Invalid regular expression, details=" + ae.Message);
                }

                if (buffer != IntPtr.Zero)
                {
                    try { Marshal.FreeHGlobal(buffer); }
                    catch { }
                    buffer = IntPtr.Zero;
                    structSize = 0;
                }
            
                //Loop through all entries, attempt to find matches
                while (true)
                {
                    long nextResult = FindNextUrlCacheEntry(hEnum, buffer, out structSize);
                    if (nextResult != 1) //TRUE
                    {
                        int lastError = Marshal.GetLastWin32Error();
                        if (lastError == ERROR_INSUFFICIENT_BUFFER)
                        {
                            buffer = Marshal.AllocHGlobal((int)structSize);
                            nextResult = FindNextUrlCacheEntry(hEnum, buffer, out structSize);
                        }
                        else if (lastError == ERROR_NO_MORE_ITEMS)
                        {
                            break;
                        }
                    }

                    result = (INTERNET_CACHE_ENTRY_INFO)Marshal.PtrToStructure(buffer, typeof(INTERNET_CACHE_ENTRY_INFO));
                    if (Regex.IsMatch(result.lpszSourceUrlName, urlPattern, RegexOptions.IgnoreCase))
                    {
                        results.Add(result);
                    }

                    if (buffer != IntPtr.Zero)
                    {
                        try { Marshal.FreeHGlobal(buffer); }
                        catch { }
                        buffer = IntPtr.Zero;
                        structSize = 0;
                    }
                }
            }
            finally
            {
                if (hEnum != IntPtr.Zero)
                {
                    FindCloseUrlCache(hEnum);
                }
                if (buffer != IntPtr.Zero)
                {
                    try { Marshal.FreeHGlobal(buffer); }
                    catch { }
                }
            }

            return results;
        }



        public static string SetupCookieCachePattern(string pattern)
        {
            const string COOKIECACHEPATTERN = ".*";
            const string DOT = ".";
            const string BACKSLASHDOT = "\\.";
            const string COOKIE = "Cookie:";

            string url = pattern;
            if (url.Length > 0)
            {
                Uri curUri = new Uri(url);
                url = curUri.Host;
                //Replace "." with "\\."
                url = url.Replace(DOT, BACKSLASHDOT);
                url = COOKIE + COOKIECACHEPATTERN + url;

                //www.google.com
                //visited:.*www\\.google\\.com

                //login.live.com
                //cookie:.*login\\.live\\.com

            }
            return url;
        }

    }
}


