import * as types from './mutation-types'
import moment from 'moment'

export const getReportsTaxCsvData = ({ commit, dispatch, state }, {hash, params}) => {
  const dateNow = moment().format('YYYY_MM_DD')
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/api/v1/reports/tax-summary-csv`, { params }, { responseType: 'blob' })
        .then((response) => {
          const url = window.URL.createObjectURL(new Blob([response.data]))
          const link = document.createElement('a')
          link.href = url
          link.setAttribute('download', `tax_report_${dateNow}.csv`)
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }

  