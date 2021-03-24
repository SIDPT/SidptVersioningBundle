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
} from '~/sidpt/versioning-bundle/plugin/versioning/store/actions'

const reducer = combineReducers({
  branches:makeReducer([],{
  	[BRANCHES_DATA_LOAD]: (state,request) => request.branches
  })
})

export {
  reducer
}
