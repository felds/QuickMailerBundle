QuickMailerBundle
=================

This bundle provides an easily configurable façade for sending transactional
emails without having to build the message from scratch every time.  

It has a limited scope by design, it's not infinitely configurable and it's not
made to work outside a typical Symfony installation.



## Instalation

### Step 0: Before you begin

Under the hood, QuickMailer uses Twig as its template engine and Swift Mailer
to actually send the messages.

Make sure that both components are installed and properly configured.


### Step 1: Download the bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require felds/quick-mailer-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.


### Step 2: Enable the bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Felds\QuickMailerBundle\FeldsQuickMailerBundle(),
        );
    }
}
```

### Step 3: Add the basic configuration

```yaml
// app/config/config.yml

quickmailer:
    from: { name: "Sender Name", email: "sender@example.com" }
```


## Usage

### Step 1: Create a template

```twig
{# app/Resources/views/email/cookie.html.twig #}

{% block subject %}
Hey, {{ name|title }}! You've got a cookie!
{% endblock %}

{% block html %}
<p>
  <strong>{{ name|title }}</strong>, take this delicious <em>{{ flavor }}</em> cookie!
</p>
{% endblock %}

{% block text %}
{{ name|title }}, take this delicious {{ flavor }} cookie!
{% endblock %}
```

### Step 2: Add it to the configuration

```yaml
# app/config/config.yml
quickmailer:
    # ...
    templates:
        cookie: email/cookie.html.twig
```

### Step 3: Send the email

```php
<?php

use Felds\QuickMailerBundle\Model\Mailable;

// assuming we're inside a controller action:

$recipient = new Mailable('Recipient', 'recipient@example.com');
$this->get('quickmailer.cookie')->sendTo($recipient, [
    'name'    => $recipient->getName(),
    'flavor'  => 'blueberry',
]);
```


## To do

### Code

- [ ] Add tests

### Docs

- [ ] Config reference
- [ ] Examples of how to integrate it with other tools
