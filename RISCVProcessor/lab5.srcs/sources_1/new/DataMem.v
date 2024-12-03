`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 05/31/2024 12:57:40 PM
// Design Name: 
// Module Name: DataMem
// Project Name: 
// Target Devices: 
// Tool Versions: 
// Description: 
// 
// Dependencies: 
// 
// Revision:
// Revision 0.01 - File Created
// Additional Comments:
// 
//////////////////////////////////////////////////////////////////////////////////


module DataMem(input MemRead, 
               input MemWrite, 
               input [8:0] addr, 
               input [31:0] write_data, 
               output [31:0] read_data);
    reg [31:0] data_mem [127:0];
    
    assign read_data = (MemRead)? data_mem[addr[8:2]] : 32'bX;
    always @(write_data, addr, MemWrite)
    begin
        if (MemWrite == 1'b1)
        begin
            data_mem[addr[8:2]] = write_data;
        end
    end
endmodule
