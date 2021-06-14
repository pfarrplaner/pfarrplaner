# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [1.60.3](https://github.com/pfarrplaner/pfarrplaner/compare/v1.60.2...v1.60.3) (2021-06-14)


### Features

* Show pastor on homescreen overviews ([c959957](https://github.com/pfarrplaner/pfarrplaner/commits/c9599575377ab85deaf79b5241d0097d43340430))


### Bug Fixes

* Baptisms/funerals/weddings from other cities appear on homescreen ([620939a](https://github.com/pfarrplaner/pfarrplaner/commits/620939aeaf540a911794936ba0bba400f8c890be))
* NextOfferingsTab is empty ([140bce5](https://github.com/pfarrplaner/pfarrplaner/commits/140bce5ede5950928c2232ca5802b27d24db6825))

### [1.60.2](https://github.com/pfarrplaner/pfarrplaner/compare/v1.60.1...v1.60.2) (2021-06-11)


### Bug Fixes

* BaptismEditor won't save correctly ([6185af7](https://github.com/pfarrplaner/pfarrplaner/commits/6185af7814d91faf504edaa82d03d7ac206ae5f7))
* Wizard buttons should use Inertia for baptism/funeral ([0e381d3](https://github.com/pfarrplaner/pfarrplaner/commits/0e381d3b055c0bf812874e372953e83487b4b3b6))

### [1.60.1](https://github.com/pfarrplaner/pfarrplaner/compare/v1.60.0...v1.60.1) (2021-06-11)


### Features

* Additional liturgical info and copyable bible references in LiturgyEditor ([d959a96](https://github.com/pfarrplaner/pfarrplaner/commits/d959a9605c6cec560c963057511b10714286ba4f))
* Show first line of FreeText items ([8c92664](https://github.com/pfarrplaner/pfarrplaner/commits/8c92664490bc131c98028fb207eeccb476024162))


### Bug Fixes

* ReferenceParser always returns single verse ranges ([d9417d9](https://github.com/pfarrplaner/pfarrplaner/commits/d9417d95acfbcc1fc206b86e3bbb28140bea12d4))

## [1.60.0](https://github.com/pfarrplaner/pfarrplaner/compare/v1.59.0...v1.60.0) (2021-06-06)


### Features

* Additional liturgical info and copyable bible references in LiturgyEditor ([091c4a6](https://github.com/pfarrplaner/pfarrplaner/commits/091c4a69e40d658f99426824abd6a3ba9d17d260))


### Bug Fixes

* Multiple fixes to ReferenceParser and BibleText services ([cd966ba](https://github.com/pfarrplaner/pfarrplaner/commits/cd966ba28c45bf4b2037d8701e7d91b36692e0f2))
* ReferenceParser fails when first verse range is optional () ([db8815e](https://github.com/pfarrplaner/pfarrplaner/commits/db8815ea9534d32438cc86d4b75dd63a759c5e43))

## [1.59.0](https://github.com/pfarrplaner/pfarrplaner/compare/v1.58.1...v1.59.0) (2021-06-06)


### Features

* Add link to Youtube's livestreaming dashboard to StreamingTab ([976fd7f](https://github.com/pfarrplaner/pfarrplaner/commits/976fd7f4158ae303980eb29d05ea651ca6e64d2b))
* LiturgySheet configuration moved to modal dialogs ([ab263c4](https://github.com/pfarrplaner/pfarrplaner/commits/ab263c41a0868881722c5ffe79dc10d607618e21))


### Bug Fixes

* AttachmentTab doesn't use Inertia for calling LiturgySheet configuration ([3a91a94](https://github.com/pfarrplaner/pfarrplaner/commits/3a91a9475f3b5c38efb28b2b52cce804fb9a0702))
* Created broadcasts have no streaming key ([47f7ecf](https://github.com/pfarrplaner/pfarrplaner/commits/47f7ecf1c4d06b435bfbaf32e2a7638f87330c4f))
* Deleting wrong verses in SongEditor ([cc40417](https://github.com/pfarrplaner/pfarrplaner/commits/cc40417feed060becebca00df206f825d6f14480)), closes [#85](https://github.com/pfarrplaner/pfarrplaner/issues/85)
* Modal dialogs default width does not scale on mobile devices ([7eb8c44](https://github.com/pfarrplaner/pfarrplaner/commits/7eb8c44d16431136aa19ae440f5e8aa816011115))
* youtube:update command does not set streaming key when previous streaming is null ([aa68ac0](https://github.com/pfarrplaner/pfarrplaner/commits/aa68ac0244ee30aeb600b7345d73a98466cc32fb))

### [1.58.1](https://github.com/pfarrplaner/pfarrplaner/compare/v1.58.0...v1.58.1) (2021-06-05)


### Bug Fixes

* FormCheck does not handle 0 values correctly ([bad74b8](https://github.com/pfarrplaner/pfarrplaner/commits/bad74b84fb6762883874ae7b7be057dcc274997f))
* LiturgySheet configuration does not correctly remember user settings ([ee5e770](https://github.com/pfarrplaner/pfarrplaner/commits/ee5e770e3209cebf7d995b961175556ed3fd9533))

## [1.58.0](https://github.com/pfarrplaner/pfarrplaner/compare/v1.57.0...v1.58.0) (2021-06-05)


### Features

* LiturgySheets (here: FullTextLiturgySheet) can optionally be configured ([d1d24ff](https://github.com/pfarrplaner/pfarrplaner/commits/d1d24ff19b3bbd2b4bd2effe2480b6f0e681516e))

## [1.57.0](https://github.com/pfarrplaner/pfarrplaner/compare/v1.56.0...v1.57.0) (2021-06-05)


### Bug Fixes

* Manual paths need to be relative in order to work on GitHub ([908c7ea](https://github.com/pfarrplaner/pfarrplaner/commits/908c7ea960a394afb455d8cda33ec2f1413471db))
* Prevent attempt to unserialize twice ([98c03ff](https://github.com/pfarrplaner/pfarrplaner/commits/98c03ff4ebac1c03180810f83776433bb5a94ef0))

## [1.56.0](https://github.com/potofcoffee/pfarrplaner/compare/v1.55.4...v1.56.0) (2021-06-05)


### Features

* Add manual builder command ([366ce5c](https://github.com/potofcoffee/pfarrplaner/commits/366ce5c78dd3dcf8e1526449c63a68b116339556))
* Allow linking to .md file paths ([b17a263](https://github.com/potofcoffee/pfarrplaner/commits/b17a2631c8a3139ee7a202b52d3d94bb5e4fbfd9))
* Basic infrastructure for an online manual ([3391ba4](https://github.com/potofcoffee/pfarrplaner/commits/3391ba4f097ba2c3fa9e32b1b1c8d385db8a48d2))
* Manual builder now creates TOC and moves images ([0b7015f](https://github.com/potofcoffee/pfarrplaner/commits/0b7015f3e0a49b15b0bc011f321e31d7400f5fc5))


### Bug Fixes

* HelpLayout should show TOC link with text ([130d8c4](https://github.com/potofcoffee/pfarrplaner/commits/130d8c40c614b258ad2e1bf20e48a97610846fb1))
* ServiceRequest fails validation when special_location field is not present ([d5cbaed](https://github.com/potofcoffee/pfarrplaner/commits/d5cbaed4d8531c148d841c4f9fbfded6abc0fbc1))
* wrong image path in manual media ([fcbef40](https://github.com/potofcoffee/pfarrplaner/commits/fcbef40355ed285cdaa9a175f940349bfce559e0))

### [1.55.4](https://github.com/potofcoffee/pfarrplaner/compare/v1.55.3...v1.55.4) (2021-06-04)


### Bug Fixes

* AbsenceEditor fails to save replacements ([ac24879](https://github.com/potofcoffee/pfarrplaner/commits/ac24879a7f31334b6b6378ceac4d7aa948cd960f))
* DateRangeInput does not set end of range ([ba2e73c](https://github.com/potofcoffee/pfarrplaner/commits/ba2e73c0f82114d69798f3fe6f3eff0ff89ccae5))
* Planner does not show replacements ([f1c9867](https://github.com/potofcoffee/pfarrplaner/commits/f1c98671ed272699a51a9f1f918b8f4587be4e43))

### [1.55.3](https://github.com/potofcoffee/pfarrplaner/compare/v1.55.2...v1.55.3) (2021-06-03)


### Features

* Automatically create CREDITS.md file ([9d438a7](https://github.com/potofcoffee/pfarrplaner/commits/9d438a7a13a846916ab446b85dc36d4236bed435))
* Show depencency links in CREDITS.md ([74eca03](https://github.com/potofcoffee/pfarrplaner/commits/74eca03fc732c4e6c6424870a7507f266602afb5))

### [1.55.2](https://github.com/potofcoffee/pfarrplaner/compare/v1.55.1...v1.55.2) (2021-06-03)

### [1.55.1](https://github.com/potofcoffee/pfarrplaner/compare/v1.55.0...v1.55.1) (2021-06-03)


### Features

* Show environment type in version info ([ac4c581](https://github.com/potofcoffee/pfarrplaner/commits/ac4c5811c056b4c056473290c1f8b6c967ad7f0d))


### Bug Fixes

* Check for registration document is shown twice in BaptismEditor ([8a020c2](https://github.com/potofcoffee/pfarrplaner/commits/8a020c2f895ea093fd6064d652e15cd8c0955aa8))

## [1.55.0](https://github.com/mokkapps/changelog-generator-demo/compare/v1.54.0...v1.55.0) (2021-06-03)


### Features

* Add new about info with changelog ([88ca927](https://github.com/mokkapps/changelog-generator-demo/commits/88ca927040dfa6b07df955133dfdfa1214307fa2))

## 1.54.0 (2021-06-03)


### Features

* Add changelog generation and semantic versioning ([146fb4e](https://github.com/mokkapps/changelog-generator-demo/commits/146fb4e1a23ed3ab5461e02272eba43237e80ea8))


### Bug Fixes

* Place blocking fails when field is empty ([4647d3d](https://github.com/mokkapps/changelog-generator-demo/commits/4647d3d51635f0a4554462f6d67f02d4232b9587))
