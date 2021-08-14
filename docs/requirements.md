---
title: Requirements
weight: 2
---

Mailcoach requires:

- PHP 8.0 or higher 
- Laravel 8 or higher
- Redis 
- MySQL 8.0 or higher
- Imagick
- ext-exif
- Laravel Horizon

If you're running Mailcoach on a load balancer and want to use Automations, make sure to enable sticky sessions as Laravel Livewire doesn't work well with load balancers.
