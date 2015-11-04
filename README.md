# phpBB-3.x-API-Registration-Mod-Revisited


Cyerus brought us this mod and it's up to us to keep it running smoothly. Pull requests highly encouraged.

Current Version 7.0.0

Requirements
-----------
```
- Apache2 Server
- Database of some sort (Preferably MYSQL)
- PHP Installed
```
Change Log From Cyerus Last Release
-----------
```
2015-11-04

7.0.0
- Project officially taken over by Mr Twinkie (Shibdib) with the blessing of Cyerus.
- API Links updated on new user registration and re-activation.
- Primary phpBB version set to 3.0.14
- Disabled part of the cron that de-activates accounts on errors as it caused a lot of false positives. (Working on a fix)
- Mod renamed to represent change in primary developer.
```

Install
-----------
```
1. Install a clean version of phpBB 3.0.14 and AUTOMOD

2. Download this as a .zip and install using AUTOMOD

3. Be sure to follow directions and remove install folder at the end.
```

Update
-----------
```
1. Uninstall EVE API 6.3.0 from the AutoMOD section of the ACP.

2. Clear forum cache

3. Upload and install EVE API 6.3.1 to the AutoMOD section of the ACP.

4. Clear forum cache again

5. Go to forum.com/install and select update

6. Once installed, delete the install folder from your forum

7. Use AutoMOD to make the required changes to your custom template (if required)

8. Cycle through each of the EVE API setting pages and save them

9. Cycle through each of the forum group setting pages and save them

10. Clear forum cache one last time, and we should be done.
```

