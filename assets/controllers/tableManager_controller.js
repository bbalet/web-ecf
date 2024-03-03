import { Controller } from '@hotwired/stimulus';
import * as Turbo from "@hotwired/turbo"

/*
 * This controller allows you to load a detail/edit page
 * on click of a row of a list of elements table.
 * and filter the rows of the table according to a search criterion.
 */
export default class extends Controller {
    
    static targets = [ "source", "filterable" ]

    filter(event) {
        let lowerCaseFilterTerm = this.sourceTarget.value.toLowerCase()
    
        this.filterableTargets.forEach((el, i) => {
          let filterableKey =  el.getAttribute("data-tableManager-key").toLowerCase()
    
          el.classList.toggle("filter--notFound", !filterableKey.includes( lowerCaseFilterTerm ) )
        })
    }

    show(event) {
        Turbo.visit(event.params.url)
    }
}
