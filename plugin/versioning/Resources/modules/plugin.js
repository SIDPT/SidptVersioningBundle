/* eslint-disable */

import {registry} from '#/main/app/plugins/registry'


registry.add('SidptVersioningBundle', {
  tools: {
    'sidpt_versioning': () => { return import(/* webpackChunkName: "plugin-sidpt-tool-versioning" */ '~/sidpt/versioning-bundle/plugin/versioning/tool/versioning') }
  }
})
