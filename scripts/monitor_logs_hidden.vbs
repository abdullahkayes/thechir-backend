Dim shell, fso, scriptDir, psExe, scriptPath, cmdLine
Set shell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")
scriptDir = fso.GetParentFolderName(WScript.ScriptFullName)
psExe = "powershell.exe"
scriptPath = fso.BuildPath(scriptDir, "monitor_logs.ps1")
cmdLine = psExe & " -NoProfile -ExecutionPolicy Bypass -NoLogo -OutputFormat None -File """ & scriptPath & """ 2>nul"
shell.Run cmdLine, 0, False
WScript.Quit
