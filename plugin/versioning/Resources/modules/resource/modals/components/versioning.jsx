import React, {Component, Fragment} from 'react'
import {PropTypes as T} from 'prop-types'
import omit from 'lodash/omit'
import get from 'lodash/get'
import set from 'lodash/set'
import cloneDeep from 'lodash/cloneDeep'

import {ResourceNode as ResourceNodeTypes} from '#/main/core/resource/prop-types'

import {trans, Translator} from '#/main/app/intl/translation'
import {Modal} from '#/main/app/overlays/modal/components/modal'
import {FormData} from '#/main/app/content/form/components/data'

import {Select} from '#/main/app/input/components/select'

// modal views
const NODE_UNMANAGED = 'unmanaged'
const BRANCH_VIEW = 'branch_view'
const BRANCH_ADD = 'branch_add'
const BRANCH_EDIT = 'branch_edit'
const VERSION_ADD = 'version_add'
const VERSION_EDIT = 'version_edit'

class VersionsManagingModal extends Component {
  
  constructor(props) {
    super(props)

    this.state = {
      stepHistory:[],
      currentStep: 'unmanaged'
    }

    this.changeStep = this.changeStep.bind(this)
  }

  stepBack(){
    const step = this.state.stepHistory.pop();
    this.setState({
      stepHistory:this.state.stepHistory.slice(),
      currentStep: step
    })
  }

  changeStep(step) {
    this.state.stepHistory.push(this.state.currentStep);
    this.setState({
      stepHistory:this.state.stepHistory.slice(),
      currentStep: step
    })
  }

  close() {
    this.props.fadeModal()
    this.changeStep('type')
    this.props.reset()
  }

  renderStepTitle() {
    switch (this.state.currentStep) {
      case NODE_UNMANAGED:
        return trans('activate_versioning', {}, 'versioning')
      case BRANCH_ADD:
        return trans('new_branch', {}, 'resource')
      case constants.RESOURCE_CREATION_RIGHTS:
        return trans('new_resource_configure_rights', {}, 'resource')
    }
  }

  

  save() {
    
    this.setState({
      data:newData
    })
    this.props.fadeModal();
  }

  // modal views
  
  

  // Main rendering scenario
  render() {
    
    return (
      <Modal
        {...omit(
          this.props, 
          'parent', 
          'newNode', 
          'saveEnabled', 
          'startCreation', 
          'updateRights', 
          'save', 
          'reset', 
          'add')}
        icon="fa fa-fw fa-plus"
        title={trans('versions_manage')}
        subtitle={this.renderStepTitle()}
        fadeModal={() => this.close()}
      >
      <div className="modal-body versioning-modal">
        {this.state.currentStep === 'unmanaged' && 
          <Fragment>
          <span></span>
          </Fragment>
        }
        
          <label for="available_branches">{trans('branch')}</label>
          <Select name="available_branches" 
              id="available_branches"
              noEmpty={true}
              onChange={this.updateSelectedField}
              value={this.state.selectedFieldIndex}
              choices={fieldchoices}
          />
        
        {this.props.defaultValues[fieldData.path] && 
          <Fragment>
          <label>{trans('default_content')}</label>
          <p className="modal-content content-meta"> {this.props.defaultValues[fieldData.path]}</p>
          </Fragment>
        }
        
        <FormData
          level={5}
          data={this.state.data}
          setErrors={() => {}}
          updateProp={this.updateProp}
          sections={[
            {
              id: 'general',
              title: '',
              primary: true,
              fields: sections
            }
          ]}
        />
          <div className="locale-selector">
            <label for="available_locales">{trans('select_new_locale')}</label>
            <Select name="available_locales" 
              id="available_locales"
              noEmpty={true}
              onChange={this.updateSelectedLocale}
              value={this.state.selectedLocale}
              choices={localeChoices}
            />
            <button
              className="btn modal-btn"
              onClick={this.addLocal}
            >
            {trans('add_new_locale')}
            </button>
          </div>
          <button
            className="modal-btn btn"
            onClick={this.save}
          >
            {trans('confirm')}
          </button>
        </div>
      </Modal>
    )}
}

VersionsManagingModal.propTypes = {
  node:T.shape(
    ResourceNodeTypes.propTypes
  ).isRequired,
  branches:T.arrayOf(T.object),
  fadeModal: T.func.isRequired,
  reset: T.func.isRequired
}

export {
  VersionsManagingModal
}