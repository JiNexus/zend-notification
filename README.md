# JiNexus/Zend-Notification

[![Build Status](https://travis-ci.org/JiNexus/zend-notification.svg?branch=master)](https://travis-ci.org/JiNexus/zend-notification)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg)](https://php.net/)
[![Latest Stable Version](https://poser.pugx.org/jinexus/zend-notification/v/stable)](https://packagist.org/packages/jinexus/zend-notification)
[![Total Downloads](https://poser.pugx.org/jinexus/zend-notification/downloads)](https://packagist.org/packages/jinexus/zend-notification)
[![License](https://poser.pugx.org/jinexus/zend-notification/license)](https://packagist.org/packages/jinexus/zend-notification)
[![Donate](https://img.shields.io/badge/donate-Paypal-blue.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5CYMGYYYS98PN)

`JiNexus/Zend-Notification` is a component that extends and utilized the components of 
`Zend-Mail`, `Zend-View`, `Zend-Config`, `Zend-Servicemanager` and `Zend-Filter` to generate 
and send a well layout emails. This component also uses `Cerberus-Responsive` as a sample 
base email template.

- File issues at https://github.com/JiNexus/zend-notification/issues
- Documentation is at https://github.com/JiNexus/zend-notification

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install `JiNexus/Zend-Notification`.

```bash
$ composer require jinexus/zend-notification
```

This will install `JiNexus/Zend-Notification` and all required dependencies. `JiNexus/Zend-Notification` requires PHP 5.6 or latest.

## Basic Usage

A basic usage consists of one or more recipients, a subject, a body/content and a sender. 
To send such a mail using `JiNexus/Zend-Notification`, do the following:

```php
<?php 
use JiNexus\Zend\Notification\Notification;

$notification = new Notification();
$notification->setFrom('sender@example.com', 'Sender Name')
    ->setTo('recipient@example.com', 'Recipient Name')
    ->setSubject('Sample Subject')
    ->setContent(
        'This is the body/content of the email, you can write here your thoughts.' .
        'I\'ve got everything yet nothing to write, for my thoughts are in a constant fight.'
    )
    ->send();
```

By default `JiNexus/Zend-Notification` is using [Zend\Mail\Transport\Sendmail](https://docs.zendframework.com/zend-mail/transport/intro/#quick-start) to send an email.

### Adding Multiple Recipients

**Method 1: Method chaining the `addTo()` method**

```php
<?php 
use JiNexus\Zend\Notification\Notification;

$notification = new Notification();
$notification->setFrom('sender@example.com', 'Sender Name')
    ->setTo('recipientOne@example.com', 'Recipient One')
    ->addTo('recipientTwo@example.com', 'Recipient Two')
    ->addTo('recipientThree@example.com', 'Recipient Three')
    ->setSubject('Your character defines you')
    ->setContent(
        'There are two things that defines you. Your patience to learn when you don\'t know anything, 
        and your attitude to share when you know everything.'
    )
    ->send();
```

**Method 2: Passing array to `setTo()` method**

```php
<?php 
use JiNexus\Zend\Notification\Notification;

$notification = new Notification();
$notification->setFrom('sender@example.com', 'Sender Name')
    ->setTo(['recipientOne@example.com', 'recipientTwo@example.com'], 'Common Name')
    ->setSubject('What makes me superior?')
    ->setContent(
        'The fact that I don\'t believe that I\'m better than anyone else 
        gives me an inevitable sense of superiority.'
    )
    ->send();
```

**Method 3: Passing array to `addTo()` method**

```php
<?php 
use JiNexus\Zend\Notification\Notification;

$notification = new Notification();
$notification->setFrom('sender@example.com', 'Sender Name')
    ->addTo(['recipientOne@example.com', 'recipientTwo@example.com'], 'Common Name')
    ->setSubject('I\'m from 90\'s')
    ->setContent(
        'Let me tell you about a magical time. The rock stars were suicidal, 
        the rappers were criminals, and wrestling was real. It was the 90\'s﻿'
    )
    ->send();
```

*Note: The difference between `setTo()` and `addTo()` method is that 
`setTo()` overwrites the existing data while `addTo()` appends to the existing data. 
In short all methods that are prefix with set's and add's behave in the same manner.*

##### You can also add recipients to carbon-copy ("Cc:") or blind carbon-copy ("Bcc:").

```php
<?php 

$notification->setCc('recipientCc@example.com', 'Recipient Cc');
$notification->setBcc('recipientBcc@example.com', 'Recipient Bcc');
```
**Or**

```php
<?php 
$notification->addCc('recipientCc@example.com', 'Recipient Cc');
$notification->addBcc('recipientBcc@example.com', 'Recipient Bcc');
```

*Note: `setCc()`, `setBcc`, `addCc()` and `addBcc` methods also accepts array of recipients
and can also be use in method chaining.*

##### If you want to specify an alternate address to which replies may be sent, that can be done, too.

```php
<?php 
$notification->setReplyTo('jimvirle@example.com', 'Jimvirle');
```

**Or**

```php
<?php 
$notification->addReplyTo('jimvirle@example.com', 'Jimvirle');
```

##### Interestingly, [RFC-822](https://www.ietf.org/rfc/rfc822.txt) allows for multiple "From:" addresses. When you do this, the first one will be used as the sender, unless you specify a "Sender:" header. The Notification class allows for this by utilizing the Zend-Mail.

```php
<?php
/*
 * Mail headers created:
 * From: Kheven Bitoon <kheven@example.com>, Rogelio Carrillo <rogelio@example.com>
 * Sender: Jimvirle Calago <jimvirle@example.com>
 */
$notification->addFrom('kheven@example.com', 'Kheven Bitoon');
$notification->addFrom('rogelio@example.com', 'Rogelio Carrillo');
$notification->setSender('jimvirle@example.com', 'Jimvirle Calago');
```

##### By default `JiNexus/Zend-Notification` provides an HTML content upon sending the email. And there are instances that you may want to choose a Text content. To do that you have to set the type of your email by:

```php
<?php 
/*
 * This will set your email to a text content.
 */
$notification->setType('text');
```

**Or**

```php
<?php 
/*
 * This will set your email to an html content.
 */
$notification->setType('html');
```

### Adding Attachments To Email

`JiNexus/Zend-Notification` directly provides you the ability to create and use mail attachments.

```php
<?php 
/*
 * You can also set multiple attachments.
 */
$notification->setAttachments([
    'absolute-path-of-the-file.jpg',
    'this-is-a-multiple-attachment.pdf',
]);
```

## Advance Usage

Advance usage allows you to assemble your own layout and set the right template to be your email's content. 
`JiNexus/Zend-Notification` uses `Cerberus-Responsive` as a sample base email template 
(You can replace it by your own choice, later when we tackle about `setConfig()` method). 
Refer to the `src/view/layout` and `src/view/template`

To do this all you have is follow the example below:

```php
<?php 
use JiNexus\Zend\Notification\Notification;

$notification = new Notification();
$notification->setFrom('sender@example.com', 'Sender Name')
    ->setTo('recipient@example.com', 'Recipient Name')
    ->setSubject('Sample Subject')
    ->assemble()
    ->send();
```

By default `JiNexus/Zend-Notification` will look to the built in configuration under `config/notification.global.php`
to feed the `assemble()` required configs. To overwrite this default configuration, you have to call the
`setConfig()` method and pass your own array of configs.

To do this all you have is follow the example below:

```php
<?php 
use JiNexus\Zend\Notification\Notification;

$notification = new Notification();
$notification->setFrom('sender@example.com', 'Sender Name')
    ->setTo('recipient@example.com', 'Recipient Name')
    ->setSubject('Sample Subject')
    ->setConfig([
            'notification' => [
                'footer' => __DIR__ . '/src/view/layout/footer.phtml',
                'header' => __DIR__ . '/src/view/layout/header.phtml',
                'layout' => __DIR__ . '/src/view/layout/layout.phtml',
                'template' => __DIR__ . '/src/view/template/confirmation-email.phtml',
            ]
        ])
    ->assemble()
    ->send();
```

*Must: Method chaining on the available methods specifically `setConfig()`, `assemble()` and `send()` has their own precedence to follow, 
they must be called in the right order:*

 - If you want to call `setConfig()`, it must be called before `assemble()` method.
 - If you want to call `assemble()`, it must be called before `send()` method.
 - And lastly, `send()` method must be called last.

*Note: Array keys are strict and must be followed accordingly, you can replace values as long as it exist.*

### Passing and Parsing a data to the Header - `header.phtml`

```php
<?php 
$notification->setHeaderData(['greetings' => 'Hello from the other side!']);
```

Now you can parse the data to your `header.phtml`, by:

```php
<?php echo $this->greetings; ?>
```

### Passing and Parsing a data to the Footer - `footer.phtml`

```php
<?php 
$notification->setFooterData([
    'company' => 'JiNexus Inc.',
    'address' => 'Cebu City, Cebu, 6000, PH',
]);
```

Now you can parse the data to your `footer.phtml`, by:

```php
<?php echo $this->company; ?>
<?php echo $this->address; ?>

```

### Passing and Parsing a data to the Template - `any-email-template.phtml`

```php
<?php 
$notification->setTemplateData([
    'fullName' => 'Jimvirle Calago',
    'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'token' => '$2y$10$2pebvLw6RhmqiybYg6qNr.F.lCIEYf0HzPrKAxNTsrRZLRI5uhh1m'
]);
```

Now you can parse the data to your `any-email-template.phtml`, by:

```php
<?php echo $this->fullName; ?>
<?php echo $this->message; ?>
<?php echo $this->token; ?>
```

*Must: `setHeaderData()`, `setFooterData()` and `setTemplateData()` must be called before the `assemble()` method.*

### Transports and setting up their own parameters or options.

Transports take care of the actual delivery of mail. Typically, you only need to worry 
about two possibilities: using PHP's native mail() functionality, which uses system 
resources to deliver mail, or using the SMTP protocol for delivering mail via a remote server.

You can define your own transport by using `setTransport()` method.

Available Values:

    - sendmail
    - smtp
    - inMemory
    
#### Sendmail

By default if you don't define your transport `JiNexus/Zend-Notification` will automatically
use sendmail. However if by any chance you want to pass a parameter to the sendmail, you can do it by:

```php
<?php 
$notification->setTransport('sendmail');
$notification->setSendmailTransportParameters('-freturn_to_me@example.com');
$notification->send();
```

**Chose your transport wisely**

Although the sendmail transport is the transport that requires least configuration, 
it may not be suitable for your production environment. This is because emails sent using 
the sendmail transport will be more often delivered to SPAM-boxes. This can partly be 
remedied by using the SMTP Transport combined with an SMTP server that has an overall good 
reputation. Additionally, techniques such as SPF and DKIM may be employed to ensure 
even more email messages are delivered successfully.

#### SMTP

Below is a sample configuration of SMTP transport:

```php
<?php 
$notification->setTransport('smtp');
$notification->setSmtpTransportOptions([
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'connection_class'  => 'plain',
    'connection_config' => [
        'username' => 'jinexus.zend@gmail.com',
        'password' => 'my-app-password',
        'ssl' => 'tls',
    ],
]);
$notification->send();
```

#### InMemory

The InMemory transport is primarily of interest when in development or when testing.

Below is a sample configuration of InMemory transport:

```php
<?php 
$notification->setTransport('inMemory');
$notification->send();
```

## Snapshots

![snapshot-01](https://cloud.githubusercontent.com/assets/6903966/25578973/242ff800-2ea6-11e7-929a-f3c48b629424.png)
![snapshot-02](https://cloud.githubusercontent.com/assets/6903966/25578972/242e6ba2-2ea6-11e7-913e-9de80ac788aa.png)

## To Do's

- Create a Unit Test
- Add Get and Set Encoding extensions
- Add Get and Set Headers extensions
- Add support for File transport
- Add getLastMessage() for InMemory transport
- Improve Documentation (Here's comes the most boring part~) #grumble

## Contributing

Before contributing please read the [Contributing File](CONTRIBUTING.md) for details.

## Security

If you discover security related issues, please email [jinexus.zend@gmail.com](mailto:jinexus.zend@gmail.com) instead of using the issue tracker.

## Credits

- [Jimvirle Calago](https://github.com/JiNexus)
- [All Contributors](../../contributors)

## Dependency

- [Zend-Mail](https://docs.zendframework.com/zend-mail)
- [Zend-View](https://docs.zendframework.com/zend-view)
- [Zend-Config](https://docs.zendframework.com/zend-config)
- [Zend-ServiceManager](https://docs.zendframework.com/zend-servicemanager)
- [Zend-Filter](https://docs.zendframework.com/zend-filter)

## License

The `JiNexus/Zend-Notification` is an open source project that is licensed under the [BSD 3-Clause License](https://opensource.org/licenses/BSD-3-Clause). See [License File](LICENSE.md) for more information.
JiNexus reserves the right to change the license of future releases.

## Change log

For the most recent change log, visit the [Releases Page](https://github.com/JiNexus/zend-notification/releases) or the [Changelog File](CHANGELOG.md). 

## Donations

Donations are **greatly appreciated!**

A man has to code for food. A man must do what he feels needs to be done, even if it is dangerous or undesirable.

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5CYMGYYYS98PN)
