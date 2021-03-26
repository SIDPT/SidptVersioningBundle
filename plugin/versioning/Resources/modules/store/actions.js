import {makeActionCreator} from '#/main/app/store/actions'


import {API_REQUEST, url} from '#/main/app/api'


const NODES_DATA_LOAD = 'NODES_DATA_LOAD'
const BRANCHES_DATA_LOAD = 'BRANCHES_DATA_LOAD'


// Front end actions 
const actions = {}
actions.loadNodes = makeActionCreator(NODES_DATA_LOAD, 'nodes')
actions.loadBranches = makeActionCreator(BRANCHES_DATA_LOAD, 'branches')


// Requests to the server (mapped from controller)
const requests = {}
requests.getNodes = () => ({
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


requests.getBranches = (nodeId) => ({
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

requests.addBranch = (nodeId, branchData = null) => ({
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

requests.updateBranch = (branchId, branchData) => ({
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

requests.deleteBranch = (branchId) => ({
  [API_REQUEST]: {
    url: ['sidpt_versioning_delete_branch',{branch:branchId}],
    request: {
      method: 'PUT',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({})
    },
    success: (data, dispatch) => {
      dispatch(actions.loadBranches(data))
    }
  }
})

requests.commit = (versionId, versionData = null) => ({
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

requests.updateVersion = (versionId, versionData) => ({
  [API_REQUEST]: {
    url: ['sidpt_versioning_update_version',{version:versionId}],
    request: {
      method: 'PUT',
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

requests.deleteVersion = (versionId) => ({
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
  actions,
  requests,
  BRANCHES_DATA_LOAD
}