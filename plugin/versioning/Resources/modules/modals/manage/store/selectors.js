import {createSelector} from 'reselect'

import {
	selectors as mainSelectors
} from '~/sidpt/versioning-bundle/plugin/versioning/store/selectors'


const selectedBranchIndex = createSelector(
  [mainSelectors.store],
  (store) => store.selectedBranchIndex
)

const versions = createSelector(
  [mainSelectors.store],
  (store) => store.selectedBranchIndex
)



export const selectors = {
	...mainSelectors,
	selectedBranchIndex,
	versions
}