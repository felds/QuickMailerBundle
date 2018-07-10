QuickMailerBundle
=================

This bundle provides an easily configurable façade for sending transactional
emails without having to build the message from scratch every time.

It has a limited scope by design, it's not infinitely configurable and it's not
made to work outside a typical Symfony installation.


## Installation

### Step 0: Before we begin

Under the hood, QuickMailer uses Twig as its template engine, SwiftMailer to
actually send the messages and Monolog to keep tabs on what is happening.

Make sure that those three bundles are installed and properly configured.


### Step 1: Download the bundle

Open a terminal, `cd` into your project directory and execute the following
command to download the latest stable version of this bundle:

```console
composer require felds/quick-mailer-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

If you are using Symfony Flex and have contrib recipes enabled, it's all done.


## Configuration

_to be done_


## Usage

### Step 1: Create a template

Using this bundle, the whole message is composed from a single twig template.

The typical message looks something like this:

```twig
{# templates/email/cookie.html.twig #}

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

Of course, you can use includes, extends and everything else available on Twig
(even your own extensions!). Just keep in mind that each main block (`subject`,
`html` and `text`) are rendered separately, and they are not visible to one
another. Ex: a `set` declared outside the `html` block will not work inside it.


### Step 2: Add it to the configuration

```yaml
# config/packages/felds_quickmailer.yaml
felds_quickmailer:
    # ...
    templates:
        cookie: email/cookie.html.twig
```

### Step 3: Send the email

```php
<?php

use Felds\QuickMailerBundle\Model\Mailable;
use Felds\QuickMailerBundle\QuickMailer;

class FooController
{
    public function barAction(QuickMailer $qm)
    {
        $recipient = new Mailable('Recipient', 'recipient@example.com');

        $qm->get('cookie')->sendTo($recipient, [
            'name'    => $recipient->getName(),
            'flavor'  => 'blueberry',
        ]);
    }
}
```


## To do

### Code

- [ ] Add tests

### Docs

- [ ] Config reference
- [ ] Examples of how to integrate it with other tools
- [ ] Guides

