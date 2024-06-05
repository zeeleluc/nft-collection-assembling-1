# NFT Collection Assembling With PHP

## Properties

### Use the following logic in the folder /properties
- /Trait Name
    - /Property a.png
    - /Property b.png
- /Another Trait
    - /Another Property for this trait a.png
    - /Another Property for this trait b.png

### Use the following logic in /properties/logic.csv

`Having Trait,With Property,Cannot Have Trait,And Property`

Properties are optional, so you can leave them empty if you want to ignore a whole 
trait - if another trait (or 1 specific property from another trait) is present.

Basically you can read it layer-wise from top to bottom. If a previous trait or
trait-property is already layered, then don't layer the following trait or trait-property:

The following rule excludes a certain Accessory if there is already a certain Hat layered:

`Hair,Punk Hair,Hat,Cowbow Hat`

Or, exclude all hats:

`Hair,Punk Hair,Hat,`