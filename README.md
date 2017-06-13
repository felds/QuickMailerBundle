Felds\\QuickMailerBundle
========================


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

            new Felds\\QuickMailerBundle(),
        );

        // ...
    }

    // ...
}
```


## Usage

### Step 1: Create the template

```twig
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

### Step 2: Create a QuickMailer service

```yaml
# app/config/services.yml
services:
    # ...
    my_quick_mailer:
        class: Felds\QuickMailerBundle\Mailer
        public: true
        arguments:
            - '@mailer'
            - '@twig'
            - 'From Name'
            - 'from-email@example.com'
            - 'email/my-template.html.twig'
```

### Step 3: Send the email

```php
<?php

use Felds\QuickMailerBundle\Model\Mailable;

  // asuming you're inside a controller action:

  $recipient = new Mailable('Recipient`s Name', 'recipient');

  $mailer = $this->get('my_quick_mailer');
  $success = $mailer->sendTo($recipient, [
      'name'    => $recipient->getName(),
      'flavor'  => 'blueberry',
  ]);

  // and it's that simple. really!!
```
