# com.megaphonetech.relateentities

![relate-entity.png screenshot](/images/relate-entity.png)

This extension allows CiviCRM's back end users to create relationships between contacts and non-contact entities. This relationship is referred to as a *Related Entity*. This is helpful for linking individuals with, for example, endowments or scholarships. Only *contact* to *financial type* relationships are present at this time, but support can be added for other entities.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.6+
* CiviCRM 5.13+

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl com.megaphonetech.relateentities@https://github.com/MegaphoneJon/com.megaphonetech.relateentities/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/MegaphoneJon/com.megaphonetech.relateentities.git
cv en relateentities
```

## Usage

Upon installation, visit any contact (regardless of the contact type), then find and click upon the newly-installed **Related Entities** tab.

![contact-new-tab.png screenshot](/images/contact-new-tab.png)

The following screen will list previously defined *entity relations*. To add a new one, simply click on the **New Entity relation** button. You will then be presented with a new screen where you can define a relationship between the contact and a non-contact entity (referred to as *Entity B*).

![relate-entity.png screenshot](/images/relate-entity.png)

## Known Issues

None.
