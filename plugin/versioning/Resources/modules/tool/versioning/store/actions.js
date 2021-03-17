import {makeActionCreator} from '#/main/app/store/actions'

import {API_REQUEST} from '#/main/app/api'


const NODES_DATA_LOAD = 'NODES_DATA_LOAD'
const BRANCHES_DATA_LOAD = 'BRANCHES_DATA_LOAD'

const actions = {}


actions.loadNodes = makeActionCreator(NODES_DATA_LOAD, 'nodes')
actions.loadBranches = makeActionCreator(BRANCHES_DATA_LOAD, 'branches')

/* ACTIONS MAPPING FROM CONTROLLER */

actions.getNodes = () => ({
  [API_REQUEST]: {
    url: ['sidpt_versioning_get_nodes'],
    request: {
      method: 'GET'
    },
    success: (data, dispatch) => {
      dispatch(actions.loadNodes(data))
    }
  }
})


actions.getBranches = (nodeId) => ({
  [API_REQUEST]: {
    url: ['sidpt_versioning_get_branches',{node:nodeId}],
    request: {
      method: 'GET'
    },
    success: (data, dispatch) => {
      dispatch(actions.loadBranches(data))
    }
  }
})

actions.addBranch = (nodeId, branchData = null) => ({
  [API_REQUEST]: {
    url: ['sidpt_versioning_add_branch',{node:nodeId}],
    request: {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        branchData
      })
    },
    success: (data, dispatch) => {
      dispatch(actions.loadBranches(data))
    }
  }
})

actions.updateBranch = (branchId, branchData) => ({
  [API_REQUEST]: {
    url: ['sidpt_versioning_update_branch',{branch:branchId}],
    request: {
      method: 'PUT',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        branchData
      })
    },
    success: (data, dispatch) => {
      dispatch(actions.loadBranches(data))
    }
  }
})

actions.deleteBranch = (branchId) => ({
  [API_REQUEST]: {
    url: ['sidpt_versioning_delete_branch',{branch:branchId}],
    request: {
      method: 'DELETE'
    },
    success: (data, dispatch) => {
      dispatch(actions.loadBranches(data))
    }
  }
})

actions.commit = (versionId, versionData = null) => ({
  [API_REQUEST]: {
    url: ['sidpt_versioning_commit',{afterVersion:versionId}],
    request: {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        versionData
      })
    },
    success: (data, dispatch) => {
      dispatch(actions.loadBranches(data))
    }
  }
})

actions.deleteVersion = (versionId) => ({
  [API_REQUEST]: {
    url: ['sidpt_versioning_delete_version',{version:versionId}],
    request: {
      method: 'DELETE'
    },
    success: (data, dispatch) => {
      dispatch(actions.loadBranches(data))
    }
  }
})

export {
  actions
}