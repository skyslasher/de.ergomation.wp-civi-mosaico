# Wordpress integration for CiviMail with Mosaico

![Screenshot](/images/screenshot.png)

This plugin integrates WordPress with the CiviCRM mail editor replacement Mosaico. The gallery now
uses the Wordpress media library.
It also contains an enhanced Versafix template with a new template block. This block comes with a
property editor that shows all available Wordpress posts. It applies the post title, excerpt with
adjustable length an a read more button with just one click.

![Screenshot](/images/screenshot_2.png)
Gallery reflecting Wordpress media in Mosaico

![Screenshot](/images/screenshot_3.png)
Corresponding Wordpress media gallery

![Screenshot](/images/screenshot_3.png)
Wordpress posts visible in Mosaico section plugin control

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* Wordpress v5.0+
* CiviCRM 5.17
* CivCRM FlexMailer plugin
* CiviCRM Mosaico plugin

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl de.ergomation.wp-civi-mosaico@https://github.com/skyslasher/de.ergomation.wp-civi-mosaico/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/skyslasher/de.ergomation.wp-civi-mosaico.git
cv en wp_civi_mosaico
```

## Usage

Open the gallery. It shows the Wordpress media library. You can also use the enhanced Versafix template with the Wordpress posting block if needed.
