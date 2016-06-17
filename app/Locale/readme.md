#### Translations

PoEdit is the recommended PO-file editor developers. 
It is able to analyze source code by looking for all occurrences from a given set of localization functions.
The string argument to these functions are the words and sentences which should be localized into other languages.
The editor is able to analyze these and present a list of unique words and sentences, which make maintenance easier. 
The project source code is in English, so the default locale is `en_US`. 

The editor stores all localized string and meta information in PO-files, one for each language. 
Each PO-file is maintained by PoEdit by recursively inspecting source code in at one or more folders relative to a base path. 

Each time you change a localized string in a folder associated with the PO-file, you have to click `Update` in the editor. 
New or modified localized string (identifiers) are detected automatically, and if possible, translated automatically 
using already translated text. When you click `Save` in the editor. 
PoEdit generates a MO-file (binary) in same locating as the PO-file.
