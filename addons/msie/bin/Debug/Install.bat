@echo off
if exist "BHOHelloWorld.dll" "C:\Windows\Microsoft.NET\Framework\v2.0.50727\regasm" "BHOHelloWorld.dll" /codebase
PAUSE