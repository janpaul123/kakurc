Attribute VB_Name = "KakuMain"
Declare Function Send Lib "TPC200L10.dll" (ByVal Scode As Byte, ByVal AanUit As Byte) As Integer

Sub Main()
    On Error GoTo help
    Dim args() As String
    args = Split(Command$, " ")
    
    Call Send(CByte(args(0)), CByte(args(1)))
    Exit Sub
    
help:
    MsgBox "call using: kaku.exe <code> <state>" + vbCrLf + "where 0<=code<=255 and state=[0,1]" + vbCrLf + "ie. kaku.exe 18 1"
End Sub
