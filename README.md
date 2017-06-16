QuickMailerBundle
=================


## Instalation

### Step 1: Download the bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require felds/quick-mailer-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

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


## Sending multiple different emails


### Step 1: Create multiple templates

```twig
{# app/Resources/views/email/base.html.twig #}

{% block subject "App Name" %}

{% block html %}
<h1>App Name</h1>

{{ block('html_content') }}
{% endblock %}

{% block text %}
App Name
========

{{ block('text_content') }}
{% endblock %}
```

```twig
{# app/Resources/views/email/cookie.html.twig #}

{% extends 'email/base.html.twig' %}

{% block subject -%}
{{ parent() }} - You received a cookie!
{%- endblock %}

{% block html_content %}
<p>Take this delicious cookie</p>
{% endblock %}

{% block text_content %}
Take this delicious cookie
{% endblock %}
```

```twig
{# app/Resources/views/email/tea.html.twig #}

{% extends 'email/base.html.twig' %}

{% block subject -%}
{{ parent() }} - Take a sip.
{%- endblock %}

{% block html_content %}
<p>Just relax. I'm bringing you some tea.</p>
{% endblock %}

{% block text_content %}
Just relax. I'm bringing you some tea.
{% endblock %}
```


### Step 2: Create multiple services

```yaml
# app/config/services.yml
services:
    # ...
    abstract_quick_mailer:
        class: Felds\QuickMailerBundle\Mailer
        public: true
        abstract: true
        arguments:
            - '@mailer'
            - '@twig'
            - 'From Name'
            - 'from-email@example.com'
            # don't fill the template parameter

    cookie_quick_mailer:
        parent: abstract_quick_mailer
        arguments:
            - 'email/cookie.html.twig'

    tea_quick_mailer:
        parent: abstract_quick_mailer
        arguments:
            - 'email/tea.html.twig'
```

### Step 3: Send the email

```php
<?php

use Felds\QuickMailerBundle\Model\Mailable;

// asuming we're inside a controller action:

$recipient = new Mailable('Recipient`s Name', 'recipient@example.com');

$mailer = $this->get('cookie_quick_mailer');
$success = $mailer->sendTo($recipient, []);
```
