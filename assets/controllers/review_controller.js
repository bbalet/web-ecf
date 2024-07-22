import { Controller } from '@hotwired/stimulus'
import * as Turbo from '@hotwired/turbo'
import Swal from 'sweetalert2'

export default class extends Controller {

    rate(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Notez le film',
            html: `
            <div>
              <label for="rating">Note :</label>
              <select id="rating">
                <option value="1" selected>1 - Mauvais</option>
                <option value="2">2 - Passable</option>
                <option value="3">3 - Bon</option>
                <option value="4">4 - Très bon</option>
                <option value="5">5 - Excellent</option>
              </select>
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea id="description" rows="4" style="width: 100%;"></textarea>
            </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Submit',
            preConfirm: () => {
                const rating = document.getElementById('rating').value;
                const description = document.getElementById('description').value;
                return { rating, description };
            }
          }).then((result) => {
            if (result.isConfirmed) {
              let url = event.params.url + `?rating=${result.value.rating}&description=${result.value.description}`;
              Turbo.visit(url);
            }
          });
    }
}
