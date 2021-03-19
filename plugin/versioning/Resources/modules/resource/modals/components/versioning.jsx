import React, {Component, Fragment} from 'react'
import {PropTypes as T} from 'prop-types'
import omit from 'lodash/omit'
import get from 'lodash/get'
import set from 'lodash/set'
import cloneDeep from 'lodash/cloneDeep'

import {ResourceNode as ResourceNodeTypes} from '#/main/core/resource/prop-types'

import {trans, Translator} from '#/main/app/intl/translation'
import {Modal} from '#/main/app/overlays/modal/components/modal'
import {Button} from '#/main/app/action/components/button'
import {CALLBACK_BUTTON, MenuButton} from '#/main/app/buttons'
import {FormData} from '#/main/app/content/form/components/data'

import {Select} from '#/main/app/input/components/select'

// modal views
const NODE_UNMANAGED = 'unmanaged'
const BRANCH_VIEW = 'branch_view'
const BRANCH_ADD = 'branch_add'
const VERSION_ADD = 'version_add'
const VERSION_EDIT = 'version_edit'

class VersionsManagingModal extends Component {
  
  constructor(props) {
    super(props)

    // versions array on reverse ordre 
    // (from the last/current version to the first one)
    const versions = []
    selectedBranchIndex = null
    if(this.props.branches.length > 0){
      let version = this.props.branches[0].head
      // if head is not the last version on the main branch
      while(version.next.length !== 0){
        version = version.next[0];
        versions.unshift(version);
      }
      // push the head
      version = this.props.branches[0].head
      versions.push(version);
      // climb back in the version tree
      while(version.previous){
        versions.push(version);
        version = version.previous;
      }
      selectedBranchIndex = 0
    }

    this.state = {
      selectedBranchIndex:selectedBranchIndex,
      selectedVersionIndex:undefined,
      versions:versions.slice(),
      currentView:selectedBranchIndex ? BRANCH_VIEW : NODE_UNMANAGED,
      newBranch:null,
      newVersion:null,
    }

    this.changeView = this.changeView.bind(this);
    this.selectBranch = this.selectBranch.bind(this);
    this.selectVersion = this.selectVersion.bind(this);
    this.close = this.close.bind(this);
    this.renderView = this.renderView.bind(this);


  }

  changeView(viewName) {
    
    switch(viewName){
      case BRANCH_ADD:
        this.setState({
          newBranch:{
            parentId:this.props.branches[0].id
          }
        })
        break;
    }
    this.setState({
      currentView:viewName
    })
  }

  selectBranch(index) {
    const versions = []
    let version = this.props.branches[index].head
    while(version.next.length !== 0){
      version = version.next[0];
      versions.unshift(version);
    }
    // push the head
    version = this.props.branches[index].head
    versions.push(version);
    // climb back in the version tree
    while(version.previous){
      versions.push(version);
      version = version.previous;
    }
    
    this.setState({
      versions:versions.slice(),
      selectedBranchIndex:index,
    })

  }

  selectVersion(index) {
    this.setState({
      selectedVersionIndex:index
    })
  }

  close() {
    this.props.fadeModal()
    this.props.reset()
  }


  renderViewTitle() {
    switch (this.state.currentView) {
      case NODE_UNMANAGED:
        return trans('node_unmanaged', {}, 'versioning');
      case BRANCH_VIEW:
        return trans('branch_view', {}, 'versioning');
      case BRANCH_ADD:
        return trans('branch_add', {}, 'versioning');
      case VERSION_ADD:
        return trans('version_add', {}, 'versioning');
      case VERSION_EDIT:
        return trans('version_edit', {}, 'versioning');
    }
    return '';
  }

  renderView() {
    switch (this.state.currentView) {
      case NODE_UNMANAGED:
        return (
          <Fragment>
              <span>{trans('unmanaged_node'),{},'versioning'}</span>
              <Button
              className="modal-btn btn"
              type={CALLBACK_BUTTON}
              primary={true}
              label={trans('activate_versioning', {}, 'versioning')}
              callback={() => {
                this.props.addBranch(this.props.node)
                this.changeView(BRANCH_VIEW);
              }}
            />
          </Fragment>
        )
      case BRANCH_VIEW:
        return(
          {this.props.branches.length > 0 && 
            <Fragment>
              <label for="available_branches">{trans('branch')}</label>
              <Select name="available_branches" 
                  id="available_branches"
                  noEmpty={true}
                  onChange={this.selectBranch}
                  value={this.state.selectedBranchIndex}
                  choices={branchList}
              />
              <div>
                <Button
                    className="modal-btn btn"
                    type={CALLBACK_BUTTON}
                    primary={true}
                    label={trans('edit_node', {}, 'versioning')}
                    callback={() => {
                      // Open the node editor for the node of the selected branch
                    }}
                  />
                <Button
                    className="modal-btn btn"
                    type={CALLBACK_BUTTON}
                    primary={true}
                    label={trans('edit_resource', {}, 'versioning')}
                    callback={() => {
                      // Open the resource editor for the resource 
                      // of the current latest version of the branch
                    }}
                  />
                <Button
                    className="modal-btn btn"
                    type={CALLBACK_BUTTON}
                    primary={true}
                    label={trans('delete_branch', {}, 'versioning')}
                    callback={() => {
                      this.props.deleteBranch(this.props.branches[this.state.selectedBranchIndex])

                    }}
                  />
              </div>
              <div>
                <Button
                    className="modal-btn btn"
                    type={CALLBACK_BUTTON}
                    primary={true}
                    label={trans('make_version', {}, 'versioning')}
                    callback={() => {
                      this.changeView(VERSION_ADD);
                    }}
                  />
                <ul>
                  {this.state.versions.map((version, index) => {
                    const isHead = version.id === this.props.branches[this.state.selectedBranchIndex].head.id
                    const isCurrent = index === 0;
                    return (
                      <li>{index}
                        {version.name ? ` - ${version.name}` : ''}
                        {isHead ? ' (head)' : ''}
                        {!isHead && <Button
                          className="modal-btn btn"
                          type={CALLBACK_BUTTON}
                          primary={true}
                          label={trans('make_head', {}, 'versioning')}
                          callback={() => {

                            let newBranch = Object.assign({},
                              this.state.newBranch ? 
                                this.state.newBranch : 
                                this.props.branches[this.state.selectedBranchIndex]
                            )
                            newBranch.head = version
                            this.setState({
                              newBranch:newBranch
                            })
                          }}
                        />}
                        </li>
                      )
                  })}
                </ul>
              </div>
              <Button
                className="modal-btn btn"
                type={CALLBACK_BUTTON}
                primary={true}
                label={trans('save', {}, 'actions')}
                disabled={this.state.newBranch === undefined}
                callback={() => {
                  if(this.state.newBranch){
                    this.props.updateBranch(
                      this.props.branches[this.state.selectedBranchIndex].id, 
                      newBranch)
                  }
                }}
              />
            </Fragment>
          }
        )
      case BRANCH_ADD:
        return (

          <Fragment>
            <FormData
              level={5}
              name={selectors.STORE_NAME}
              dataPart={this.state.newBranch}
              sections={[
                {
                  title:trans('new_branch')
                  primary:true,
                  fields:[
                    {
                      name:'name',
                      label:trans('branch_name', {}, 'versioning'),
                      type:'string',
                      required:true
                    }
                  ]
                }
              ]}
            />
            <Button
              className="modal-btn btn"
              type={CALLBACK_BUTTON}
              primary={true}
              label={trans('save', {}, 'actions')}
              disabled={!this.props.saveEnabled}
              callback={() => {
                this.props.addBranch(
                  this.props.node.id,
                  this.state.newBranch)
                this.changeView(BRANCH_VIEW)
              }}
            />
          </Fragment>
        )
      case VERSION_ADD:
        return (
          <Fragment>
            <FormData
              level={5}
              name={selectors.STORE_NAME}
              dataPart={this.state.newVersion}
              sections={[
                {
                  title:trans('new_version')
                  primary:true,
                  fields:[
                    {
                      name:'name',
                      label:trans('branch_name', {}, 'versioning'),
                      type:'string'
                    },
                    {
                      name:'isHead',
                      label:trans('branch_head', {}, 'versioning'),
                      type:'boolean'
                    }
                  ]
                }
              ]}
            />
            <Button
              className="modal-btn btn"
              type={CALLBACK_BUTTON}
              primary={true}
              label={trans('save', {}, 'actions')}
              disabled={!this.props.saveEnabled}
              callback={() => {
                this.props.addVersion(
                  this.state.versions[0].id, 
                  this.state.newVersion)
                this.changeView(BRANCH_VIEW)
              }}
            />
          </Fragment>
        )
      case VERSION_EDIT:
        return (
          <Fragment>
            <FormData
              level={5}
              name={selectors.STORE_NAME}
              dataPart={this.state.newVersion}
              sections={[
                {
                  title:trans('new_version')
                  primary:true,
                  fields:[
                    {
                      name:'name',
                      label:trans('version_name', {}, 'versioning'),
                      type:'string'
                    },
                    {
                      name:'isHead',
                      label:trans('branch_head', {}, 'versioning'),
                      type:'boolean'
                    }
                  ]
                }
              ]}
            />
            <Button
              className="modal-btn btn"
              type={CALLBACK_BUTTON}
              primary={true}
              label={trans('save', {}, 'actions')}
              disabled={!this.props.saveEnabled}
              callback={() => {
                this.props.updateVersion(this.state.node.id,this.state.newVersion)
              }}
            />
          </Fragment>
        )

    }
  }



  
  

  // Main rendering scenario
  render() {
    const branchList = this.props.branches.map((branch) => {
      return branch.name
    })
    return (
      <Modal
        {...omit(
          this.props,
          'node',
          'branches', 
          'addBranch',
          'updateBranch',
          'deleteBranch',
          'addVersion',
          'updateVersion',
          'editNode',
          'editResource',
          'reset'
          )}
        icon="fa fa-fw fa-plus"
        title={trans('versions_manage')}
        subtitle={this.renderViewTitle()}
        fadeModal={() => this.close()}
      >
      <div className="modal-body versioning-modal">
        {this.renderView()}
  
        <Button
          className="modal-btn btn"
          type={CALLBACK_BUTTON}
          primary={true}
          label={trans('cancel')}
          disabled={!this.props.saveEnabled}
          callback={() => this.close()}
        />
      </div>
      </Modal>
    )}
}

VersionsManagingModal.propTypes = {
  node:T.shape(
    ResourceNodeTypes.propTypes
  ).isRequired,
  // branches is managed by reducer
  branches:T.arrayOf({
    id:T.number,
    name:T.string,
    resourceNode:T.object,
    parentId:T.string,
    head:T.object
  }),
  // Functions needed by the modal
  addBranch:T.func,
  updateBranch:T.func,
  deleteBranch:T.func,
  addVersion:T.func,
  updateVersion:T.func,

  editNode:T.func,
  editResource:T.func,

  fadeModal: T.func.isRequired,
  reset: T.func.isRequired
}

export {
  VersionsManagingModal
}