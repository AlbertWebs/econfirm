import React, { useState } from 'react';

function SecureTransactionForm(props) {
    const [waitingForSTK, setWaitingForSTK] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setWaitingForSTK(true);
        // ...existing code to send STK request...
        // When STK callback is received, hide spinner:
        // setWaitingForSTK(false);
    };

    // You should call setWaitingForSTK(false) when the callback is received.
    // ...existing code...

    return (
        <form onSubmit={handleSubmit}>
            {/* ...existing form fields... */}
            {waitingForSTK && <span className="spinner"></span>}
            {/* ...existing code... */}
            <button type="submit" disabled={waitingForSTK}>Submit</button>
        </form>
    );
}

export default SecureTransactionForm;