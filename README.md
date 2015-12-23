# CorporateVCardsBundle
This Bundle allows you to easily create simple & professional looking vcards.

## Let's get started
To install this bundle inside your existent symfony2 project, follow these instructions:

1. Add this project to composer

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/AtlanteGroup/CorporateVCardsBundle"
        }
    ],
    "require": {
        # ...
        "atlantegroup/corporate-vcards-bundle": "master"
    }

2. Register this bundle:

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
            prefix:   /vcards/

### Defining profiles
We're now ready to configure each profile:

    # app/config/config.yml
    corporate_v_cards:
        config:                                     # BUNDLE CONFIGURATION
            mail_service: @app.mails                # Set to null to disable mails
            favicons:
                enabled: true
                real_favicon_generator_api_key: null
                dir: bundles/app/img/vcards-favicons/
            backgrounds:
                - bundles/app/img/vcards-backgrounds/1.jpg
                - bundles/app/img/vcards-backgrounds/2.jpg
                - bundles/app/img/vcards-backgrounds/3.jpg
                - bundles/app/img/vcards-backgrounds/4.jpg
                - bundles/app/img/vcards-backgrounds/5.jpg
            
        default:                                    # DEFAULT PROFILE INFORMATION
            url: http://my_website.com
            
        profiles:                                   # PROFILES
            jdoe:
                firstName: John
                lastName: Doe
                photo: bundles/app/img/vcards-people/jdoe.jpg

### Assetic
This bundle uses assetic to reduce CSS & JS files loading times. This bundle must be explicitly
added to assetic bundles in order to make it generate assets correctly:

    # app/config/config.yml
    assetic:
        bundles: [ AppBundle, CorporateVCardsBundle ]

### Mails
If enabled, you can send a link to the vcard by e-mail by using the included mail form. You must handle sending the mail
in your own bundle, by implementing the `MailsServiceInterface`'s `sendVcard` function. It received three arguments:

 - `$toMail`: form-submitted e-mail address
 - `$profile`: person's information: `[ 'firstName' => 'John', 'lastName' => 'Doe', /* ... */ ]`
 - `$person`: person's name as defined in your config.yml file: `jdoe` 

### Favicons
Each profile can have custom favicons generated form its profile picture, using [RealFaviconGenerator](https://realfavicongenerator.net/)'s API.
In order to use this feature, you must generate your own **Non-interactive API key** [here](https://realfavicongenerator.net/api/).

Once done, run the `cvc:generate-favicons` command to generate them. 
