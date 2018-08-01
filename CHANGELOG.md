# Changelog

## 1.0.0-rc.13 - 2018-08-01
### Changed
- TransformerHelper inherits the Flux TransformerHelper which assist in resolving a transformer from Yii config

## 1.0.0-rc.12 - 2018-07-18
### Changed
- Moved redundant integration login into separate package

## 1.0.0-rc.11 - 2018-07-15
### Changed
- Using Transform v3 package

## 1.0.0-rc.10 - 2018-07-12
### Fixed
- Cache was not getting properly created when removing from contact list

## 1.0.0-rc.9 - 2018-07-10
### Fixed
- Issue when syncing to HubSpot a false positive response would show to the user

## 1.0.0-rc.8 - 2018-07-09
### Changed
- ObjectAssociation record will interpret the objects when trying to resolve attribute ids

## 1.0.0-rc.7 - 2018-07-09
### Fixed
- Ensure a string is returned when calling ObjectFromElementAccessor::getId
- ObjectsField::normalizeQueryInputValue should return a record, not a query 

## 1.0.0-rc.6 - 2018-07-09
### Fixed
- Error were not getting set properly on failed resource sync up/down operations 
- Bug when no saving object field settings without selecting any actions

## 1.0.0-rc.5 - 2018-07-09
### Fixed
- Issue when no min or max values are set, the association query is aborted.

### Changed
- Converted criteria to 'accessor' criteria which is intended to retrieve data
- Moved converted the concept of 'builders' as mutator criteria
- Aligned resource services to new 'mutator' criteria which is intended to alter data
 
## 1.0.0-rc.4 - 2018-07-06
### Added
- Contact batch support
- Raw sync methods

## 1.0.0-rc.3 - 2018-07-05
### Removed
- Deprecated criteria interface methods
- `ObjectCriteriaInterface::setId()` method

### Fixed
- Contact::syncTo operation would error if contact already existed in HubSpot.

### Added
- ObjectFromElementCriteria class which assists with getting a HubSpot object from an Element and Field.

## 1.0.0-rc.2 - 2018-07-03
### Changed
- Altering various Object Association class method names

### Added
- Field actions to Objects field

## 1.0.0-rc.1 - 2018-07-02
### Added
- API limits via settings
- When adding a timeline event, the id and event type id are automatically added to the payload.

## 1.0.0-rc - 2018-06-28
- Initial release!
