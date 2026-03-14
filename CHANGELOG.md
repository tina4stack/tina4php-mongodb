# Changelog

## [2.0.2] - 2026-03-14

### Fixed
- Removed hardcoded production credentials
- Removed all debug echo/print_r statements
- Fixed getLastId() to track _id from insertOne()
- Fixed error() to store and return real DataError objects
- Implemented getDatabase() with listCollectionNames()
- Fixed exec() for CREATE TABLE, DELETE, UPDATE, INSERT
- Fixed fetch() with limit/offset support

### Added
- Complete rewrite of DataMongoDb driver
- Uniform test suite (16 tests, 23 assertions)
- Docker compose for local testing (mongo:7 on port 37017)


## [2.0.2] - 2026-03-14

### Added
- Uniform database driver test suite with Docker support
- phpunit.xml configuration
- GitHub Actions CI workflow
- MIT LICENSE file
- Added proper Docker Compose configuration
- Rewrote test suite (16 tests)

### Changed
- Removed redundant classmap autoloading (PSR-4 only)
- Added PHP >= 8.1 requirement to composer.json

### Fixed
- Removed hardcoded production credentials
- Removed debug echo/print_r statements
- Fixed getLastId(), error(), getDatabase(), exec(), tableExists(), fetch()
