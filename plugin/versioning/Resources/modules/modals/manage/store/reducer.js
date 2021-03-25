import {
	makeInstanceAction
} from '#/main/app/store/actions'

import {
	combineReducers, makeReducer
} from '#/main/app/store/reducer'

import {
	makeFormReducer
} from '#/main/app/content/form/store/reducer'

import {
	reducer as branchesReducer,
	BRANCHES_DATA_LOAD
} from '~/sidpt/versioning-bundle/plugin/versioning/store/'

import {
	BRANCH_SELECTED,
  VERSION_SELECTED
} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/store/actions'

import {
	selectors
} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/store/selectors'

const buildVersionsList = (branches, index) => {
	const versions = []
    let version = branches[index].head
    while(version.next.length !== 0){
      version = version.next[0];
      versions.unshift(version);
    }
    // push the head
    version = branches[index].head
    versions.push(version);
    // climb back in the version tree
    while(version.previous){
      versions.push(version);
      version = version.previous;
    }
    return versions;
}

const reducer = combineReducers({
  branches:makeReducer([],{
    [BRANCHES_DATA_LOAD]: (state,request) => request.branches
  }),
  selectedBranchIndex:makeReducer(null,{
  	[BRANCHES_DATA_LOAD]: (state,request) => {
  		if(request.branches.length > 0){
  			return 0;
  		} else return null;
  	},
  	[BRANCH_SELECTED]: (state,action) => {
  		return action.selectedBranchIndex
  	}
  }),
  versions:makeReducer([],{
    [BRANCHES_DATA_LOAD]: (state,request) => {
      if(request.branches.length > 0){
        return buildVersionsList(request.branches, 0)
      } else return [];
    },
    [BRANCH_SELECTED]: (state,action) => {
      if(action.selectedBranchIndex && state.branches.length > 0){
        return buildVersionsList(selectors.branches(state),action.selectedBranchIndex)
      } else return [];
    }
  }),
  selectedVersionIndex:makeReducer(null,{
    [BRANCHES_DATA_LOAD]: (state,request) => {
      if(request.branches.length > 0){
        return 0;
      } else return null;
    },
    [BRANCH_SELECTED]: (state,action) => {
      return 0;
    },
    [VERSION_SELECTED]: (state,action) => {
      return action.selectedVersionIndex;
    }
  })
})

export {
	reducer
}

