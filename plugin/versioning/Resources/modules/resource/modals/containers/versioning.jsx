import {connect} from 'react-redux'

import {withReducer} from '#/main/app/store/components/withReducer'

import {VersionsManagingModal as VersionsManagingModalComponent} from '~/sidpt/versioning-bundle/plugin/versioning/modals/components/versioning'

import {actions,requests,reducer,selectors} from '~/sidpt/versioning-bundle/plugin/versioning/store'

const VersionsManagingModal = withReducer(selectors.STORE_NAME, reducer)(
  connect(
    (state) => ({
    }),
    (dispatch) => ({
      // Requests sent to controller
      addBranch(nodeId, branchData = null){
        dispatch(requests.addBranch(nodeId, branchData)
      },
      updateBranch(branchId, newBranchData){
        dispatch(requests.updateBranch(branchId, newBranchData)
      },
      deleteBranch(branchId){
        dispatch(requests.deleteBranch(branchId)
      },
      addVersion(afterVersionId, newVersionData){
        dispatch(requests.addVersion(afterVersionId, newVersionData)
      },
      updateVersion(versionId,newVersionData){
        dispatch(requests.updateVersion(versionId, newVersionData)
      }
    })
  )(VersionsManagingModalComponent)
)

export {
  VersionsManagingModal
}
