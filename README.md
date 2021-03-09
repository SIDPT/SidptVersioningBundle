# SIDPT Versioning plugin for claroline LMS

This repo holds the versioning dashboard plugin of the IPIP platform for the Claroline LMS.


## functionnalities

This plugin aims to provide the following functionalities to Claroline LMS

### Version editor tool
- Mark a resource node as versioned
    + Select a node in any available workspace
    + Create a 'main' branch for the selected Node
    + Create a starting version for the resource pointing to the node
- Edit a branch for a selected versioned node
    + Rename the branch
    + Select the version to display
    + Add a new resource version
        * Create a a copy of the current resource state
        * Create a new version and associate it with the resource copy
        * Change the head of the selected branch
        * Move the resource node reference from the previous resource state to the new one (nullify the previous state resourceNode reference)
    + Add a new branch from the current one (and optionnaly from a selected version) (may be reserved and hidden under "Add a translation")
        * Create a copy of the resource node under the resource node himself
        * Create a copy of the selected resource state and associate the copy with the new resource node
        * Create the child branch to be associated with the new resource node
        * Create a new head version for the new branch and associate it with the resource copy
    + Delete a version (and its followers) on a specified branch
        * If the displayed version was in the list, move the resource node reference of the branch
    + Delete a child branch and its associated versions for a selected resource node

### Version selector
The select should be applied for now on Binder and Document resources
- When serializing a node, if the node is versioned
    + If the node has a child branch named after the user current locale
        * Get the branch referenced node instead of the serialized target
    + Else keep the selected node
  

  
  
  
 

