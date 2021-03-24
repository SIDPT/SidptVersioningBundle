import {createSelector} from 'reselect'

const STORE_NAME = 'sidpt_versioning'

const store = (state) => state[STORE_NAME]

const branches = createSelector(
  [store],
  (store) => store.branches
)


export const selectors = {
	STORE_NAME,
	branches,
	store
}