import {makeActionCreator} from '#/main/app/store/actions'

import {
  actions,
  requests
} from '~/sidpt/versioning-bundle/plugin/versioning/store/'

const BRANCH_SELECTED = 'BRANCH_SELECTED'

actions.selectBranch = makeActionCreator(BRANCH_SELECTED,'selectedBranchIndex');

export {
	actions,
	BRANCH_SELECTED
}
