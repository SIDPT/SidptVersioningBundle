import React from 'react'
import {PropTypes as T} from 'prop-types'
import omit from 'lodash/omit'

import {Routes} from '#/main/app/router'

import {MenuSection} from '#/main/app/layout/menu/components/section'


const VersioningMenu = props => {

  return (
    <MenuSection
      {...omit(props)}
      title={trans('versioning')}
    >
    
    </MenuSection>
  );
}
  


VersioningMenu.propTypes = {

  // from menu
  opened: T.bool.isRequired,
  toggle: T.func.isRequired,
  autoClose: T.func.isRequired
}

export {
  VersioningMenu
}
