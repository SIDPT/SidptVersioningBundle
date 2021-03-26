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
	BRANCHES_DATA_LOAD
} from '~/sidpt/versioning-bundle/plugin/versioning/store/'

import {
	BRANCH_SELECTED,
  VERSION_SELECTED
} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/store/actions'

import {
	selectors
} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/store/selectors'



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

