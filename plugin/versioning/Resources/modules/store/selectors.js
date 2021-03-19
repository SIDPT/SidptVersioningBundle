import {createSelector} from 'reselect'

const STORE_NAME = 'sidpt_versioning'

const store = (state) => state[STORE_NAME]

export const selectors = {
	STORE_NAME,
	store
}