import {makeInstanceAction} from '#/main/app/store/actions'
import {combineReducers, makeReducer} from '#/main/app/store/reducer'
import {makeFormReducer} from '#/main/app/content/form/store/reducer'

import {selectors} from '~/sidpt/versioning-bundle/plugin/versioning/tool/versioning/store/selectors'


const reducer = combineReducers({
  branches:makeReducer([],{
  	[BRANCHES_LOAD]: (state,request) => request.branches
  })
})

export {
  reducer
}
