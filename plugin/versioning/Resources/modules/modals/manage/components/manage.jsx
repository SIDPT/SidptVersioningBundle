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

import {
  selectors
} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/store/selectors'

// modal views
const BRANCH_VIEW = 'branch_view'
const BRANCH_ADD = 'branch_add'
const VERSION_ADD = 'version_add'
const VERSION_EDIT = 'version_edit'

class VersionsManagingModal extends Component {
  
  constructor(props) {
    super(props)
        
    this.state = {
      currentView:BRANCH_VIEW,
      newBranch:null,
      newVersion:null,
    }

    this.changeView = this.changeView.bind(this);
    this.close = this.close.bind(this);
    this.renderView = this.renderView.bind(this);

    this.reloadBranches = this.reloadBranches.bind(this);

    


  }

  reloadBranches(){
    this.setState({
      currentView:BRANCH_VIEW,
      newBranch:null,
      newVersion:null,
    })
  }

  changeView(viewName) {
    
    switch(viewName){
      case BRANCH_ADD:
        this.setState({
          newBranch:{
            parentId:this.props.branches[0].id,
            name:'new_branch'
          }
        })
        break;
      case VERSION_ADD:
        this.setState({
          newVersion:{
            data:{}
          }
        })
        break;
    }
    this.setState({
      currentView:viewName
    })
  }




  close() {
    this.props.fadeModal()
    //this.props.reset()
  }


  renderViewTitle() {
    switch (this.state.currentView) {
      case BRANCH_VIEW:
        return trans(this.props.branches.length > 0 ? 'branch_view' : 'unmanaged_node', {}, 'versioning');
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
      case BRANCH_VIEW:
        if(this.props.branches.length === 0){
          return (
            <Fragment>
                <Button
                className="modal-btn btn"
                type={CALLBACK_BUTTON}
                primary={true}
                label={trans('activate_versioning', {}, 'versioning')}
                callback={() => {
                  this.props.addBranch(this.props.node.id)
                  this.changeView(BRANCH_VIEW);
                }}
              />
            </Fragment>
          )
        } else {
          console.log(this.props.branches)
          const branchList = {}
          this.props.branches.forEach((branch) => {
            branchList[branch.name] = branch.name
          })
          
          return(
            <Fragment>
              <label htmlFor="available_branches">{trans('branch')}</label>
              <Select name="available_branches" 
                  id="available_branches"
                  noEmpty={true}
                  onChange={(index)=>this.props.selectBranch(index)}
                  value={this.props.selectedBranchIndex}
                  choices={branchList}
              />
              <div className="branch-actions">
                <Button
                  className="btn"
                  type={CALLBACK_BUTTON}
                  primary={true}
                  label={trans('add_branch', {}, 'versioning')}
                  callback={() => {
                    this.changeView(BRANCH_ADD);
                  }}
                />
                <Button
                    className="btn"
                    type={CALLBACK_BUTTON}
                    primary={true}
                    label={trans('edit_node', {}, 'versioning')}
                    callback={() => {
                      // Open the node editor for the node of the selected branch
                    }}
                  />
                <Button
                    className="btn"
                    type={CALLBACK_BUTTON}
                    primary={true}
                    label={trans('edit_resource', {}, 'versioning')}
                    callback={() => {
                      // Open the resource editor for the resource 
                      // of the current latest version of the branch
                    }}
                  />
                <Button
                    className="btn"
                    type={CALLBACK_BUTTON}
                    primary={true}
                    label={trans('delete_branch', {}, 'versioning')}
                    callback={() => {
                      this.props.deleteBranch(this.props.branches[this.props.selectedBranchIndex].id)

                    }}
                  />
              </div>
              <div className='branch-versions'>
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
                  {this.props.versions.map((version, index) => {
                    const isHead = version.id === this.props.branches[this.props.selectedBranchIndex].head.id
                    const isCurrent = index === 0;
                    return (
                      <li key={`version_${index}`}>{index}
                        {version.name ? ` - ${version.name}` : ''}
                        {isHead ? ' (head)' : ''}
                        {!isHead && <Button
                          className="btn"
                          type={CALLBACK_BUTTON}
                          primary={true}
                          label={trans('make_head', {}, 'versioning')}
                          callback={() => {
                            let newBranch = {
                              data:Object.assign({},this.state.newBranch.data ? 
                                this.state.newBranch.data : 
                                this.props.branches[this.props.selectedBranchIndex]
                              )
                            }
                            newBranch.data.head = version
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
                      this.props.branches[this.props.selectedBranchIndex].id, 
                      this.state.newBranch.data)
                  }
                }}
              />
            </Fragment>
          )
        }
      case BRANCH_ADD:
        return (
          <Fragment>
            <FormData
              level={5}
              name={selectors.STORE_NAME}
              data={this.state.newBranch}
              updateProp={(prop,value)=>{

                console.log(prop)
                console.log(value)
                let tempData = cloneDeep(this.state.newBranch);
                console.log(tempData)
                tempData[prop] = value;
                console.log(tempData)
                this.setState({
                  newBranch:tempData
                })
              }}
              setErrors={()=>{}}
              sections={[
                {
                  title:trans('new_branch'),
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
              disabled={this.state.newBranch.name === ""}
              callback={() => {
                this.props.addBranch(
                  this.props.node.id,
                  this.state.newBranch.data)
                this.changeView(BRANCH_VIEW)
              }}
            />
            <Button
              className="modal-btn btn"
              type={CALLBACK_BUTTON}
              primary={true}
              label={trans('cancel')}
              callback={() => this.changeView(BRANCH_VIEW)}
            />
          </Fragment>
        )
      case VERSION_ADD:
        return (
          <Fragment>
            <FormData
              level={5}
              name={selectors.STORE_NAME}
              data={this.state.newVersion}
              updateProp={(prop,value)=>{
                let tempVersion = cloneDeep(this.state.newVersion);
                tempVersion[prop] = value;
                console.log(tempVersion)
                this.setState({
                  newVersion:tempVersion
                })
              }}
              sections={[
                {
                  title:trans('new_version'),
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
                this.props.addVersion(
                  this.props.versions[0].id, 
                  this.state.newVersion.data)
                this.changeView(BRANCH_VIEW)
              }}
            />
            <Button
              className="modal-btn btn"
              type={CALLBACK_BUTTON}
              primary={true}
              label={trans('cancel')}
              callback={() => this.changeView(BRANCH_VIEW)}
            />
          </Fragment>
        )
      case VERSION_EDIT:
        return (
          <Fragment>
            <FormData
              level={5}
              name={selectors.STORE_NAME}
              data={this.state.newVersion}
              sections={[
                {
                  title:trans('new_version'),
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
            <Button
              className="modal-btn btn"
              type={CALLBACK_BUTTON}
              primary={true}
              label={trans('cancel')}
              callback={() => this.changeView(BRANCH_VIEW)}
            />
          </Fragment>
        )

    }
  }



  
  

  // Main rendering scenario
  render() {
    
    return (
      <Modal
        {...omit(
          this.props,
          'node',
          'branches',
          'versions',
          'selectedBranchIndex',
          'selectBranch',
          'getBranches',
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
        onEntering={ () => {
          this.props.getBranches(this.props.node.id)
        }}
      >
      <div className="modal-body versioning-modal">
        {this.renderView()}
      </div>
      </Modal>
    )}
}

VersionsManagingModal.propTypes = {
  node:T.shape(
    ResourceNodeTypes.propTypes
  ).isRequired,
  // REDUCER
  branches:T.arrayOf(
    T.shape({
      id:T.string,
      name:T.string,
      resourceNode:T.object,
      parentId:T.string,
      head:T.object
    })
  ),
  selectedBranchIndex:T.number,
  versions:T.arrayOf(T.object),
  selectedVersionIndex:T.number,
  // Functions needed by the modal
  getBranches:T.func,
  selectBranch:T.func,
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