Mocks change log
================

## ?.?.? / ????-??-??

## 8.0.1 / 2022-02-27

* Made library compatible with XP 11, `xp-framework/collections` 10.0.0
  (@thekid)

## 8.0.0 / 2020-04-11

* Implemented xp-framework/rfc#334: Drop PHP 5.6:
  . **Heads up:** Minimum required PHP version now is PHP 7.0.0
  . Rewrote code base, grouping use statements
  . Rewrote `isset(X) ? X : default` to `X ?? default`
  . Converted `newinstance` to anonymous classes
  (@thekid)

## 7.0.1 / 2020-04-04

* Made compatible with XP 10 - @thekid

## 7.0.0 / 2018-01-09

* Fixed PHP 7.2 compatibility - @thekid
* **Heads up:** Dropped PHP 5.5 support - @thekid
* Made this library compatible with XP9 - @thekid

## 6.6.1 / 2016-09-20

* Fix build w/ XP 7 - @kiesel

## 6.6.0 / 2016-09-20

* Make compatible w/ newer XP Framework versions - @kiesel, @friebe

## 6.5.2 / 2016-05-02

* Merged PR #1: Declare static initializer in a MockProxy - @kiesel

## 6.5.1 / 2016-01-23

* Fixed test suite for forward compatibility with XP7 - @thekid

## 6.5.0 / 2015-12-20

* **Heads up**: Changed minimum XP version to run webtests to XP
  6.5.0, and with it the minimum PHP version to PHP 5.5
  (@thekid)
* Added dependency on collectons library which has since been extracted
  from XP core.
  (@thekid)

## 6.4.2 / 2015-08-06

* **Heads up: Split library from xp-framework/core as per xp-framework/rfc#293**
  (@thekid)
