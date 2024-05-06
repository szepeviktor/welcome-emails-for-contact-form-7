=== Welcome emails for Contact Form 7 ===
Tags: welcome emails, email series, automation
Tested up to: 6.5.2
Stable tag: 1.0.1
Contributors: satollo
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create beautiful welcome emails for Contact Form 7 to be sent immediately and delayed.

== Description ==

Your contacts deserve a stunning welcome! With Welcome Emails you can create stylish emails with updated content from your blog, your logo and colors and... a second delayed welcome email to keep the contact engaged.

= Main Features =

* Integrated with Contact Form 7
* Emails configurable for each Contact Form 7 form
* Immediate and delayed emails
* Drag and drop email designer (no code, no HTML)
* Fully responsive emails
* Customizable sender email and name
* Uses the native WP mailing functionality no need for external services or accounts
* Compatible with every SMTP plugin (WP Mail SMTP, Fluent SMTP, Offload SES, ...)

= To Do =

* Placeholders to inject form data (there are security concerns to be evaluated)
* Integration with other form managers
* Delay for the first welcome email

= Support =

To send ideas, critiques, support requests, virtual coffees, ...

* [My contact page](https://www.satollo.net/about)
* [Official site and documentation](https://www.satollo.net/plugins/automation)

= GDPR, Privacy, Data, ... =

Welcome Emails does not send data to external sites, everything is stored in your WordPress site database.

= Credits =

 * Logo by [Freepik](https://www.freepik.com/free-vector/flat-design-electronics-logos-pack_14322078.htm) (rielaborated)

== Frequently Asked Questions ==

= It doesn't work! =

Don't worry (probably) I can solve the problem. Kindly [contact me](https://www.satollo.net/about) with as many information as possible (logs, screenshots, and so on).

= I don't understand this or that =

Kindly [contact me](https://www.satollo.net/about) so I can help and update the documentation.

= No emails are sent =

If the blog can send emails (for example the password recovery email), try to empty the sender name and email on the plugin settings.

Don't use email addresses like @gmail.com, @yahoo.com, ..., since your server is not allowed to send in behalf of those providers.

If you're using an SMTP plugin, run some tests from that plugin.

Install [WP Mail Logging](https://wordpress.org/plugins/wp-mail-logging/) to check if emails are generated.

= Delayed emails are not sent in time =

A few hours of tolerance is acceptable if your blog has not enough traffic and no custom scheduler trigger has been setup: the internal WP scheduler depends on those factors.

Check the Tools/Site Health panel for warning like "a job has failed to start" and contact the provider to
deal with the problem and setup a cron trigger for WP (there is a lot of literature about this issue, they know what it means).

Install [WP Crontrol](https://wordpress.com/plugins/wp-crontrol/) to check the status of the WordPress scheduler.

= Emails do not look good on Outlook, Gmail, ... =

I do my best to create fully compatible emails, but clients, especially Outlook and Gmail used to read non
Google mail accounts, have a lot of limitation. Anyway, send us a screenshot using my
[contact page](https://www.satollo.net/about), we're more than happy to check and solve the problem, is possible.

= Is there a text part for emails? =

No, currently emails are only in HTML.

= Why the composer is so simple? =

In my option a welcome email needs to be simple and clear, easy readable on mobile devices and with immediate calls to action.
And... is was extracted from the original project I've no resources to make it more complex right now (smile).

== Screenshots ==

1. The list of forms to be connected to a welcome email series
2. Configuration of a series connected to a form
3. Editing of an email with the composer
4. History of form submissions and emails sent or to be sent

== Changelog ==

= 1.0.1 =

* Fixed some translatable strings
* Fixed the wrong "late" status report for the engine
* Fixed the plugin description
* Fixed the loading of JS and CSS

= 1.0.0 =

* First public release
