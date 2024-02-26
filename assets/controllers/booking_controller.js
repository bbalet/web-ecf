import { Controller } from '@hotwired/stimulus'
import * as Turbo from "@hotwired/turbo"

/*
 * This controller is used to manage the booking of seats in a cinema room.
 * The first step is to select the seats that the user wants to book.
 * If the user clicks on a seat, the seat is selected and the image of the seat is changed to a selected image.
 * If the user clicks on a selected seat, the seat is unselected and the image of the seat is changed to an unselected image.
 * The user can select several seats.
 * The user can then click on the "Book" button to book the selected seats.
 */
export default class extends Controller {
    static targets = [ "seat" ]
    static values = { actionUrl: String, imgWheelchairSelected: String, imgWheelchair: String, imgSeatSelected: String, imgSeat: String }

    /**
     * This method is called when the controller is connected to the page.
     * Intialize the clas variables
     */
    connect() {
        this.selectedSeatsString = ''
        this.nbOfSelectedSeats = 0
    }

    /**
     * Toggle the selection of the seat
     * @param {*} event 
     */
    toggleBook(event) {
        let img = event.currentTarget
        let type = event.params.type
        if (img.dataset.selected ==="true") {
            if (type == "wheelchair") {
                img.src = this.imgWheelchairValue
            } else {
                img.src = this.imgSeatValue
            }
            img.dataset.selected = "false"
        } else {
            if (type == "wheelchair") {
                img.src = this.imgWheelchairSelectedValue
            } else {
                img.src = this.imgSeatSelectedValue
            }
            img.dataset.selected = "true"
        }
        this.updateSelectedSeatsString()
    }

    /**
     * Update the selected seats string to be passed to the booking controller
     */
    updateSelectedSeatsString() {
        let selectedSeats = this.seatTargets.filter(seat => seat.dataset.selected === "true")
        this.selectedSeatsString = selectedSeats.map(seat => seat.dataset.bookingSeatidParam).join(",")
        this.nbOfSelectedSeats = selectedSeats.length
    }

    /**
     * Book the selected seats (call the backend controller)
     */
    book() {
        if (this.nbOfSelectedSeats > 0) {
            let url = this.actionUrlValue + this.selectedSeatsString
            Turbo.visit(this.actionUrlValue.replace('%2E', this.selectedSeatsString))
        }
    }
}
