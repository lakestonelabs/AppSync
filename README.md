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

### And coding style tests

Explain what these tests test and why

```
Give an example
```

## Deployment

Add additional notes about how to deploy this on a live system

## Built With

* [Dropwizard](http://www.dropwizard.io/1.0.2/docs/) - The web framework used
* [Maven](https://maven.apache.org/) - Dependency Management
* [ROME](https://rometools.github.io/rome/) - Used to generate RSS Feeds

## Contributing

Please read [CONTRIBUTING.md](https://gist.github.com/PurpleBooth/b24679402957c63ec426) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## Authors

* **Billie Thompson** - *Initial work* - [PurpleBooth](https://github.com/PurpleBooth)

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* Hat tip to anyone whose code was used
* Inspiration
* etc


