import {makeActionCreator} from '#/main/app/store/actions'

import {
  actions,
  requests
} from '~/sidpt/versioning-bundle/plugin/versioning/store/'

const BRANCH_SELECTED = 'BRANCH_SELECTED'
const VERSION_SELECTED = 'VERSION_SELECTED'

actions.selectBranch = makeActionCreator(BRANCH_SELECTED,'selectedBranchIndex');
actions.selectVersion = makeActionCreator(VERSION_SELECTED,'selectedVersionIndex');

export {
	actions,
	BRANCH_SELECTED,
	VERSION_SELECTED
}
