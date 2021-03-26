import {createSelector} from 'reselect'

import {
	selectors as mainSelectors
} from '~/sidpt/versioning-bundle/plugin/versioning/store/selectors'


const selectedBranchIndex = createSelector(
  [mainSelectors.store],
  (store) => store.selectedBranchIndex
)


const selectedVersionIndex = createSelector(
  [mainSelectors.store],
  (store) => store.selectedVersionIndex
)

export const selectors = {
	...mainSelectors,
	selectedBranchIndex,
	selectedVersionIndex
}