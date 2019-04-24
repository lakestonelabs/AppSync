# AppSync
A CLI program that installs/upgrades Android apps from one device to another using ADB.  This program
is great if you don't want to use Google's Play Store on you primary-driver phone and you can use AppSync to side-load programs from another phone that has Google Play Store.

AppSync utilizes the "Andorid Debugging Bridge" (ADB) to communicate with the various phones.  Both USB and network mediums are supported for communication.  

## Getting Started

Download/Clone and run appsync.

### Prerequisites

* A Linux box to run AppSync.  Windows support is coming.
* ADB installed on Linux.  This can be installed in most distributions under the "android-tools" package.
* PHP CLI >= 7.x
* Two mor more Android phones.  One phone must have Google Play Store installed.
* Developer mode enabled on phones.  You'll need this in order to utilize the adb protocol.  [Go here](https://developer.android.com/studio/command-line/adb) to see how to enable ADB on your device.
* USB cables for each Android device (if using USB mode).
* Local LAN connectivity (if using network mode.).

### Installing

Download/Clone AppSync

## Using AppSync

Make sure ADB is enabled on both phones.

### Using network mode

Once you have ADB network mode enabled on both phone, run AppSync on your box by changing to the
location you downloaded AppSync to and run the below command.

```
./appsync -n
```

AppSync will connect to both phones and get the listings of apps.  It will then ask you which phone
is the source (phone you want to copy the app from) and the destination (the phone you want to
install the app to).  

From there follow the on-screen instructions on which apps you would like to install and/or upgrade.

## Authors

* **Mike Lee** - *Lakestone Labs* - [LakestoneLabs](https://github.com/lakestonelabs)

## License

This project is licensed under the GPL 3 license - see the [LICENSE.md](gpl.md) file for details


