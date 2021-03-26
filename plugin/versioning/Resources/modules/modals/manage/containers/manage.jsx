import {connect} from 'react-redux'

import {withReducer} from '#/main/app/store/components/withReducer'

import {
  VersionsManagingModal as VersionsManagingModalComponent
} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/components/manage'

import {
  actions,
  requests
} from '~/sidpt/versioning-bundle/plugin/versioning/store/'

import {
  actions as modalActions
} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/store/actions'

import {
  selectors
} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/store/selectors'

import {
  reducer
} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/store/reducer'

const VersionsManagingModal = withReducer(selectors.STORE_NAME, reducer)(
  connect(
    (state) => ({
      branches:selectors.branches(state),
      selectedBranchIndex:selectors.selectedBranchIndex(state),
      selectedVersionIndex:selectors.selectedVersionIndex(state),
    }),
    (dispatch) => ({
      selectBranch(branchIndex){
        dispatch(modalActions.selectBranch(branchIndex))
      },
      // Requests sent to controller
      getBranches(nodeId){
        dispatch(requests.getBranches(nodeId))
      },
      addBranch(nodeId, branchData = null){
        dispatch(requests.addBranch(nodeId, branchData))
      },
      updateBranch(branchId, newBranchData){
        dispatch(requests.updateBranch(branchId, newBranchData))
      },
      deleteBranch(branchId){
        dispatch(requests.deleteBranch(branchId))
      },
      addVersion(afterVersionId, newVersionData){
        dispatch(requests.commit(afterVersionId, newVersionData))
      },
      updateVersion(versionId,newVersionData){
        dispatch(requests.updateVersion(versionId, newVersionData))
      }
    })
  )(VersionsManagingModalComponent)
)

export {
  VersionsManagingModal
}
