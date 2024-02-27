import { Controller } from '@hotwired/stimulus'
import Masonry from 'masonry-layout'
import * as Turbo from '@hotwired/turbo'
import Swal from 'sweetalert2'

/*
 * This controller is used to manage the movies feature
 * 
 * 
 */
export default class extends Controller {
    static values = { actionUrl: String, baseUrl: String }


    /**
     * This method is used to setup masonry layout for the movies page
     * Because we use Hotwire Turbo, the document's onload event is not triggered when navigating back to the movies page.
     * So we need to use the Turbo's lifecycle events to initialize the masonry layout.
    */
    connect() {
        this.theaterId = null
        this.genreId = null
        this.dayNumber = null
        const msnry = new Masonry('#moviesDeck', { percentPosition: true })
    }

    /**
     * On Changing the value of any of the filters
     */
    setFilters() {
        this.theaterId = document.querySelector('#cboTheater').value
        this.genreId = document.querySelector('#cboGenre').value
        this.dayNumber = document.querySelector('#cboDay').value
        Turbo.visit(this.actionUrlValue + '?theaterId=' + this.theaterId + '&genreId=' + this.genreId + '&dayNumber=' + this.dayNumber)
    }

    /**
     * Click on the "See sessions" button
     */
    seeSessions(event) {
        this.theaterId = document.querySelector('#cboTheater').value
        let args = []
        if (this.theaterId != 0) {
            Turbo.visit(this.baseUrlValue + '/booking/theaters/' + this.theaterId + '/movies/' + event.params.movieId);
        } else {
            Swal.fire("Choisissez un cinéma pour voir les séances", "", "warning");
        }
    }
}
