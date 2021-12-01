---
title: Requirements
weight: 2
---

Mailcoach on traditional hosting requires:

- PHP 8.0 or higher 
- Laravel 8 or higher
- Redis 
- MySQL 8.0 or higher
- Imagick
- ext-exif
- Laravel Horizon

If you're running Mailcoach on a load balancer and want to use Automations, make sure to enable sticky sessions as Laravel Livewire doesn't work well with load balancers.

Mailcoach on Vapor requires:

- PHP 8.0 or higher
- Docker (installed locally to deploy to Vapor)
- Laravel 8 or higher
- Redis
- MySQL 8.0 or higher
- Imagick
- ext-exif
- Laravel Horizon

