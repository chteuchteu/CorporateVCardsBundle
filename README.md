# CorporateVCardsBundle
This Bundle allows you to easily create simple & professional looking vcards:

![CorporateVCardsBundle](screenshot.png)

## Features

 - Produces a set of webpages for each defined profile: (with a customizable prefix)
    - /vcard/jppernaut
    - /vcard/cchazal
    - /vcard/pparvor
 - On click, shows a QR Code containing all the profile information in the vcard vcf format
 - Easily download the .vcf file to be opened in any contacts management tool
 - Easily share the page by sending it as e-mail to a client, friend, relative, ...

## Let's get started
To install this bundle inside your existent symfony2 project, follow these instructions:

1. Require project in your `composer.json` file

        # composer.json
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/AtlanteGroup/CorporateVCardsBundle"
            }
        ],
        "require": {
            # ...
            "atlante-group/corporate-vcards-bundle": "dev-master"
        }

2. Register this bundle in symfony's kernel:

        // app/AppKernel.php
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new AtlanteGroup\CorporateVCardsBundle\CorporateVCardsBundle(),
            );
        }

3. Configure routing:

        # app/config/routing.yml
        CorporateVCardsBundle:
            resource: "@CorporateVCardsBundle/Resources/config/routing.yml"
            prefix:   /vcard/

4. Install assets:

        $ php app/console asset:install


### Defining profiles
We're now ready to configure this bundle. The `config` node sets general configuration, while `default` and `profiles`
defines profiles-related information.

First, define define some - or none - default values using the `default` node. Then, create one or several profiles inside
the `profiles` node. A valid profile tree would look like this:

    bdumaurier:
        firstName: Bedelia
        lastName: Du Maurier
        company: Hannibal & Associates
        jobTitle: CEO
        email: bdumaurier@hannibal.com
        phone:
            mobile: +33 6 12 34 56 78
            work: +33 3 12 34 56 78
        address:
            street: 3706 Merry Cider Round
            city: Silverado
            region: California
            zip: 92676
            country: US
        photo: bundles/app/img/vcards-people/bdumaurier.jpg
        url: http://www.my-website.com

All these three nodes produces the following configuration tree view:

    # app/config/config.yml
    corporate_v_cards:
        config:                                     # BUNDLE CONFIGURATION
            mails_service: @app.mails                # Set to null to disable mails
            favicons:
                enabled: true
                real_favicon_generator_api_key: null
                dir: @AppBundle/Resources/public/img/vcards-favicons/
            backgrounds:
                - bundles/app/img/vcards-backgrounds/1.jpg
                - bundles/app/img/vcards-backgrounds/2.jpg
                - bundles/app/img/vcards-backgrounds/3.jpg
                - bundles/app/img/vcards-backgrounds/4.jpg
                - bundles/app/img/vcards-backgrounds/5.jpg
            
        default:                                    # DEFAULT PROFILE INFORMATION
            company: My Company
            phone:
                work: +33 3 45 67 89 10
            url: http://my_website.com
            
        profiles:                                   # PROFILES
            jdoe:
                firstName: John
                lastName: Doe
                photo: bundles/app/img/vcards-people/jdoe.jpg

> Warning: all URIs must be formatted as above (`@AppBundle/Resources/public/file.ext` vs `bundles/app/file.ext` format,
trailing and leading slashes)

### Mails
If enabled, a form will be shown on each vcard's page, allowing one to send the current vcard to an e-mail address.
You must handle sending the mail in your own bundle, by implementing the `MailsServiceInterface`'s `sendVcard` function. It received three arguments:

 - `$toMail`: form-submitted e-mail address
 - `$profile`: person's information: `[ 'firstName' => 'John', 'lastName' => 'Doe', /* ... */ ]`
 - `$person`: person's name as defined in your config.yml file: `jdoe` 

### Favicons
Each profile can have custom favicons generated from its profile picture, using [RealFaviconGenerator](https://realfavicongenerator.net/)'s API.
In order to use this feature, you must generate your own **Non-interactive API key** [here](https://realfavicongenerator.net/api/).

It is recommended to configure `config.favicons.dir` to be a directory in your own bundle, so you can add generated assets to source control.

Once run, the `cvc:generate-favicons` command will loop over each one of the configured profiles, and call RealFaviconGenerator's API
if the current profile **has a photo and hasn't any generated favicons yet**.

> Note 1: you can force regenerating favicons for a profile by deleting its dir content

> Note 2: you must execute this command if you add new profiles, change a photo, or rename a profile key.

### Email protection
In order to protect profiles e-mail addresses against spammer robots, we "encrypt" them using [ROT13](https://en.wikipedia.org/wiki/ROT13) 
substitution cipher. Those are then decoded client-side using a simple javascript script. This technique is not meant to be perfect
nor secure, but to avoid displaying clear e-mail addresses in the vcards HTML source.

## Contribute
All contributions are welcomed! Please create your pull-requests against the `devel` branch.
